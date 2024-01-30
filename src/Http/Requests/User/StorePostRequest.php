<?php

namespace Weblebby\Framework\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Unique;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Enums\PostStatusEnum;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Facades\Theme;
use Weblebby\Framework\Models\Post;
use Weblebby\Framework\Services\User\PostFieldService;
use Weblebby\Framework\Support\Features;

class StorePostRequest extends FormRequest
{
    public readonly PostInterface $postable;

    public readonly array $rulesAndAttributes;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->postable && $this->user()->can($this->postable::getPostAbilityFor('create'));
    }

    protected function prepareForValidation(): void
    {
        $this->transformSlug();
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
            'fields.slug' => [
                'nullable', 'string', 'max:191',
                (new Unique('post_translations', 'slug'))
                    ->when(
                        $this->route('post'),
                        fn (Unique $unique, $post) => $unique->ignore($post->translate($this->input('_locale')))
                    ),
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

        if (Extension::has('multilingual')) {
            $locales = \Weblebby\Extensions\Multilingual\Facades\Localization::getSupportedLocales()->pluck('code');

            $rules['_locale'] = ['sometimes', 'required', 'string', new In($locales ?? [])];
        }

        if ($this->postable::doesSupportTemplates() && panel()->supports(Features::themes())) {
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

    protected function transformSlug(): void
    {
        if ($this->has('fields.slug')) {
            $this->merge([
                'fields' => [
                    ...$this->input('fields', []),
                    'slug' => Str::slug($this->input('fields.slug')),
                ],
            ]);
        }
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
        if (! $this->has('_deleted_fields')) {
            return;
        }

        $this->merge([
            '_deleted_fields' => collect($this->input('_deleted_fields'))
                ->map(fn ($field) => Str::after($field, 'fields.'))
                ->toArray(),
        ]);
    }
}
