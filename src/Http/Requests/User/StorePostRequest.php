<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Enums\PostStatusEnum;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Theme;
use Feadmin\Models\Post;
use Feadmin\Services\User\PostFieldService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Unique;

class StorePostRequest extends FormRequest
{
    readonly public PostInterface $postable;

    readonly public array $rulesAndAttributes;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->postable && $this->user()->can($this->postable::getPostAbilityFor('create'));
    }

    protected function prepareForValidation(): void
    {
        $this->loadRulesAndAttributes();
        $this->transformTaxonomies();
        $this->transformDeletedFields();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $taxonomies = collect($this->postable::getTaxonomies())->pluck('name');

        $rules = [
            'title' => ['required', 'string', 'max:191'],
            'slug' => [
                'nullable', 'string', 'max:191',
                (new Unique('posts'))
                    ->where('type', $this->postable::getModelName())
                    ->when($this->route('post'), fn(Unique $query, $post) => $query->ignore($post)),
            ],
            'content' => ['nullable', 'string', 'max:65535'],
            'taxonomies' => ['nullable', 'array', 'max:5'],
            'taxonomies.*.taxonomy' => ['required', 'string', new In($taxonomies)],
            'taxonomies.*.terms' => ['required', 'array', 'max:20'],
            'taxonomies.*.terms.*' => ['required', 'string', 'max:191'],
            'status' => ['required', new Enum(PostStatusEnum::class)],
            'featured_image' => ['nullable', 'image', 'max:10240'],
            'published_at' => ['nullable', 'date'],
            'position' => ['nullable', 'integer'],
            '_deleted_fields' => ['nullable', 'array'],
            '_deleted_fields.*' => ['required', 'string', 'max:191'],
            '_reordered_fields' => ['nullable', 'array'],
            '_reordered_fields.*' => ['required', 'string', 'max:191'],
        ];

        if ($this->postable::doesSupportTemplates()) {
            $templates = Theme::active()->templatesFor($this->postable::class)->pluck('name');
            $rules['template'] = ['nullable', 'string', new In($templates)];
        }

        if ($this->postable::getTaxonomyFor('category')) {
            $rules['primary_category'] = [
                'nullable',
                (new Exists('taxonomies', 'id'))
                    ->where('taxonomy', $this->postable::getTaxonomyFor('category')->name()),
            ];
        }

        return array_merge($this->rulesAndAttributes['rules'], $rules);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'title' => __('Başlık'),
            'slug' => __('URL'),
            'content' => __('İçerik'),
            'taxonomies' => __('Etiketler'),
            'template' => __('Şablon'),
            'status' => __('Durum'),
            'published_at' => __('Yayınlanma Tarihi'),
            'position' => __('Sıra'),
        ];

        return array_merge($this->rulesAndAttributes['attributes'], $attributes);
    }

    protected function loadRulesAndAttributes(): void
    {
        /** @var PostInterface $post */
        if ($post = $this->route('post')) {
            $this->postable = $post;
        } else {
            $this->postable = PostModels::find($this->input('postable', Post::getModelName()));
        }

        /** @var PostFieldService $postFieldService */
        $postFieldService = app(PostFieldService::class);
        $this->rulesAndAttributes = $postFieldService->rulesAndAttributes($this->postable, $this->input());
    }

    protected function transformTaxonomies(): void
    {
        $taxonomies = [];

        foreach ($this->taxonomies ?? [] as $taxonomy => $terms) {
            $taxonomies[] = [
                'taxonomy' => $taxonomy,
                'terms' => $terms,
            ];
        }

        $this->merge(['taxonomies' => $taxonomies]);
    }

    protected function transformDeletedFields(): void
    {
        if (!$this->has('_deleted_fields')) {
            return;
        }

        $this->merge([
            '_deleted_fields' => collect($this->input('_deleted_fields'))
                ->map(fn($field) => Str::after($field, 'fields.'))
                ->toArray(),
        ]);
    }
}
