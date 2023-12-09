<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Concerns\Postable;
use Feadmin\Enums\PostStatusEnum;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Theme;
use Feadmin\Models\Post;
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

        if ($this->postable::doesSupportTemplates() && $this->template) {
            $template = Theme::active()
                ->templatesFor($this->postable::class)
                ->firstWhere('name', $this->template);

            $sections = array_merge($sections, $template->sections()->toArray());
        }

        foreach ($sections as $section) {
            $rules = array_merge($rules, $this->postable->fieldsForValidation($section['fields'], $this->all())['rules']);
        }

        dd($rules);

        return $rules;
    }
}
