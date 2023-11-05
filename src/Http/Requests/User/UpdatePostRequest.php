<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Enums\PostStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('post:update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('posts')
                    ->where('type', 'post')
                    ->ignore($this->post->id)
            ],
            'content' => ['required', 'string', 'max:65535'],
            'status' => ['required', Rule::enum(PostStatusEnum::class)],
            'published_at' => ['nullable', 'date'],
            'position' => ['nullable', 'integer'],
        ];
    }
}
