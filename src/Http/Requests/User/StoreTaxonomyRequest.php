<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Facades\PostModels;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxonomyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $taxonomy = PostModels::taxonomy($this->taxonomy);

        return $taxonomy && $this->user()->can($taxonomy->abilityFor('create'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('terms')],
            'description' => ['nullable', 'string', 'max:65535'],
            'parent_id' => [
                'nullable', 'integer',
                Rule::exists('taxonomies', 'id')
                    ->where('taxonomy', $this->taxonomy)
            ],
        ];
    }
}
