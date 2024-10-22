<?php

namespace Weblebby\Framework\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Weblebby\Framework\Services\User\AppearanceEditorService;

class UpdateAppearanceEditorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('appearance:editor:update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required', 'string', 'max:191',
                function ($attribute, $value, $fail) {
                    /** @var AppearanceEditorService $appearanceEditorService */
                    $appearanceEditorService = app(AppearanceEditorService::class);

                    if (is_null($appearanceEditorService->getFile($value))) {
                        $fail(__('Dosya bulunamadı.'));
                    }
                },
            ],
            'content' => ['required', 'string', 'max:65535'],
        ];
    }
}
