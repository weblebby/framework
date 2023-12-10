<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Concerns\Postable;
use Feadmin\Enums\PostStatusEnum;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Theme;
use Feadmin\Models\Post;
use Feadmin\Services\FieldInputService;
use Feadmin\Services\FieldValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    readonly public Postable $postable;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->postable = PostModels::find($this->input('postable', Post::getModelName()));

        return $this->user()->can($this->postable::getPostAbilityFor('create'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:191'],
            'slug' => [
                'required', 'string', 'max:191',
                Rule::unique('posts')->where('type', $this->postable::getModelName()),
            ],
            'content' => ['required', 'string', 'max:65535'],
            'status' => ['required', Rule::enum(PostStatusEnum::class)],
            'published_at' => ['nullable', 'date'],
            'position' => ['nullable', 'integer'],
        ];

        $sections = $this->postable::getPostSections()->toArray();

        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);
        /** @var FieldValidationService $fieldValidationService */
        $fieldValidationService = app(FieldValidationService::class);

        if ($this->postable::doesSupportTemplates() && $this->template) {
            $template = Theme::active()
                ->templatesFor($this->postable::class)
                ->firstWhere('name', $this->template);

            $sections = array_merge($sections, $template->sections()->toArray());
        }

        foreach ($sections as $section) {
            $mappedFields = $fieldInputService->mapFieldsWithInput($section['fields'], $this->all());
            $rules = array_merge($rules, $fieldValidationService->get($section['fields'], $mappedFields)['rules']);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        $attributes = [
            'title' => __('Başlık'),
            'slug' => __('URL'),
            'content' => __('İçerik'),
            'status' => __('Durum'),
            'published_at' => __('Yayınlanma Tarihi'),
            'position' => __('Sıra'),
        ];

        $sections = $this->postable::getPostSections()->toArray();

        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);
        /** @var FieldValidationService $fieldValidationService */
        $fieldValidationService = app(FieldValidationService::class);

        if ($this->postable::doesSupportTemplates() && $this->template) {
            $template = Theme::active()
                ->templatesFor($this->postable::class)
                ->firstWhere('name', $this->template);

            $sections = array_merge($sections, $template->sections()->toArray());
        }

        foreach ($sections as $section) {
            $mappedFields = $fieldInputService->mapFieldsWithInput($section['fields'], $this->all());
            $attributes = array_merge($attributes, $fieldValidationService->get($section['fields'], $mappedFields)['attributes']);
        }

        return $attributes;
    }
}
