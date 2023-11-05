<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Enums\NavigationTypeEnum;
use Feadmin\Facades\NavigationLinkable;
use Feadmin\Models\NavigationItem;
use Feadmin\Services\NavigationService;
use Illuminate\Foundation\Http\FormRequest;
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
            'link' => null,
            'linkable_type' => null,
            'linkable_id' => null,
        ]);
    }

    protected function prepareForCustomLink(): void
    {
        $this->merge([
            'type' => NavigationTypeEnum::LINK->value,
            'linkable_type' => null,
            'linkable_id' => null,
            'smart_type' => null,
            'smart_limit' => null,
        ]);
    }

    protected function prepareForLinkable(): void
    {
        $json = json_decode($this->linkable);
        $linkable = NavigationLinkable::linkables()->firstWhere('id', $json->linkable_type);

        $this->merge([
            'type' => NavigationTypeEnum::LINKABLE->value,
            'linkable_type' => $linkable['model'],
            'linkable_id' => $json->linkable_id,
            'smart_type' => null,
            'smart_limit' => null,
            'link' => null,
        ]);
    }

    protected function prepareForHomepage(): void
    {
        $this->merge([
            'type' => NavigationTypeEnum::HOMEPAGE->value,
            'linkable_type' => null,
            'linkable_id' => null,
            'smart_type' => null,
            'smart_limit' => null,
            'link' => null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $smartMenuItemKeys = resolve(NavigationService::class)
            ->smartMenuItems()
            ->pluck('id');

        $models = NavigationLinkable::linkables()->pluck('model');
        $model = $models->filter(fn($model) => $model === $this->linkable_type)->first();

        return [
            'parent_id' => ['nullable', Rule::exists(NavigationItem::class, 'id')],
            'title' => ['required', 'string', 'max:191'],
            'is_smart_menu' => ['nullable', 'boolean'],
            'linkable_type' => ['nullable', 'string', 'max:191', Rule::in($models)],
            'linkable_id' => [
                'nullable',
                Rule::requiredIf($this->type === NavigationTypeEnum::LINKABLE->value),
                Rule::exists($model, 'id')
            ],
            'link' => [
                'nullable', 'max:191', 'nullable',
                Rule::requiredIf($this->type === NavigationTypeEnum::LINK->value)
            ],
            'smart_type' => [
                'nullable', 'string', 'max:191',
                Rule::requiredIf($this->type === NavigationTypeEnum::SMART->value), Rule::in($smartMenuItemKeys)
            ],
            'smart_limit' => [
                'nullable', 'numeric', 'max:10',
                Rule::requiredIf($this->type === NavigationTypeEnum::SMART->value)
            ],
            'is_active' => ['nullable', 'boolean'],
            'open_in_new_tab' => ['nullable', 'boolean'],
        ];
    }
}
