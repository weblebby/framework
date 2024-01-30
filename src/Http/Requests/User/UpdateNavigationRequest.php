<?php

namespace Weblebby\Framework\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Weblebby\Framework\Models\Navigation;

class UpdateNavigationRequest extends FormRequest
{
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
    // protected function prepareForValidation(): void
    // {
    //     $this->merge([
    //         'handle' => Str::slug($this->handle),
    //     ]);
    // }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'handle' => [
                'nullable', 'string', 'max:191',
                Rule::unique(Navigation::class)->ignore($this->route('navigation')),
            ],
        ];
    }
}
