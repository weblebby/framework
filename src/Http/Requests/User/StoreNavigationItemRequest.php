<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Enums\NavigationTypeEnum;
use Feadmin\Facades\NavigationLinkable;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\SmartMenu;
use Feadmin\Models\NavigationItem;
use Feadmin\Services\NavigationService;
use Feadmin\Services\TaxonomyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ValidatedInput;
use Illuminate\Validation\Rule;

class StoreNavigationItemRequest extends FormRequest
{
    /**
     * The key to be used for the view error bag.
     *
     * @var string
     */
    protected $errorBag = 'item';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('navigation:update');
    }

    /**
     * Get validated data with casts.
     */
    public function safeWithCasts(): ValidatedInput
    {
        if ($this->smart_condition) {
            /** @var TaxonomyService $taxonomyService */
            $taxonomyService = app(TaxonomyService::class);

            $smartFilterValues = array_map(fn($filter) => $filter['value'], $this->smart_filters);
            $terms = $taxonomyService->createMissingTaxonomies($this->smart_condition, $smartFilterValues);

            $data['smart_filters'] = collect($terms)
                ->groupBy('taxonomy')
                ->map(fn($terms) => $terms->pluck('id')->unique()->values())
                ->toArray();
        }

        return $this->safe()->merge($data ?? []);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'open_in_new_tab' => $this->has('open_in_new_tab'),
        ]);

        if ($this->is_smart_menu === '1') {
            $this->prepareForSmart();

            return;
        }

        if (blank($this->linkable)) {
            $this->prepareForCustomLink();

            return;
        }

        if ($this->linkable === 'homepage') {
            $this->prepareForHomepage();

            return;
        }

        $this->prepareForLinkable();
    }

    protected function prepareForSmart(): void
    {
        $this->merge([
            'type' => NavigationTypeEnum::SMART->value,
            'smart_filters' => json_validate($this->smart_filters) ? json_decode($this->smart_filters, true) : [],
            'smart_view_all' => $this->has('smart_view_all'),
        ]);
    }

    protected function prepareForCustomLink(): void
    {
        $this->merge(['type' => NavigationTypeEnum::LINK->value]);
    }

    protected function prepareForLinkable(): void
    {
        $value = json_decode($this->linkable, true);
        $linkable = NavigationLinkable::linkables()->firstWhere('model', $value['linkable_type']);

        $this->merge([
            'type' => NavigationTypeEnum::LINKABLE->value,
            'linkable_type' => $linkable->model(),
            'linkable_id' => $value['linkable_id'],
        ]);
    }

    protected function prepareForHomepage(): void
    {
        $this->merge(['type' => NavigationTypeEnum::HOMEPAGE->value]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'parent_id' => ['nullable', Rule::exists(NavigationItem::class, 'id')],
            'title' => ['required', 'string', 'max:191'],
            'is_smart_menu' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'open_in_new_tab' => ['nullable', 'boolean'],
        ];

        if ($this->type === NavigationTypeEnum::LINKABLE->value) {
            $linkableModels = NavigationLinkable::linkables()->pluck('model');
            $selectedLinkableModelIndex = $linkableModels->search($this->linkable_type);

            $rules = array_merge($rules, [
                'linkable_type' => ['required', 'string', 'max:191', Rule::in($linkableModels)],
                'linkable_id' => ['required', Rule::exists($linkableModels[$selectedLinkableModelIndex], 'id')],
            ]);
        }

        if ($this->type === NavigationTypeEnum::LINK->value) {
            $rules = array_merge($rules, [
                'link' => ['required', 'string', 'max:191'],
            ]);
        }

        if ($this->type === NavigationTypeEnum::SMART->value) {
            $smartMenuNames = SmartMenu::items()->pluck('name');
            $smartConditions = collect(PostModels::find($this->smart_type)->getTaxonomies())->pluck('name');

            $rules = array_merge($rules, [
                'smart_type' => ['required', 'string', 'max:191', Rule::in($smartMenuNames)],
                'smart_condition' => ['nullable', 'string', 'max:191', Rule::in($smartConditions)],
                'smart_filters' => ['nullable', 'array'],
                'smart_filters.*.label' => ['nullable', 'string', 'max:191'],
                'smart_filters.*.value' => ['nullable', 'max:191'],
                'smart_sort' => ['nullable', 'array'],
                'smart_sort.*' => ['nullable', 'string', 'max:191'],
                'smart_view_all' => ['nullable', 'boolean'],
                'smart_limit' => ['required', 'numeric', 'max:10'],
            ]);
        }

        return $rules;
    }
}
