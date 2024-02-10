<?php

namespace Weblebby\Framework\Services\User;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\In;
use Weblebby\Framework\Facades\Preference;
use Weblebby\Framework\Items\Field\Collections\FieldCollection;
use Weblebby\Framework\Services\FieldInputService;
use Weblebby\Framework\Services\FieldValidationService;

class SetupService
{
    protected ?array $steps = null;

    public function steps(): array
    {
        if ($this->steps) {
            return $this->steps;
        }

        $steps = [
            'site' => __('Site'),
        ];

        if (filled(theme()->installmentPreferenceKeys())) {
            $steps['theme'] = __('Theme');
        }

        if (filled(theme()->variants())) {
            $steps['variant'] = __('Variant');
        }

        return $this->steps = $steps;
    }

    public function getCurrentStep(string $step): string
    {
        $steps = array_keys($this->steps());

        return in_array($step, $steps) ? $step : head($steps);
    }

    public function getPrevStep(string $currentStep): ?string
    {
        $steps = array_keys($this->steps());
        $index = array_search($currentStep, $steps);

        return $steps[$index - 1] ?? null;
    }

    public function getNextStep(string $currentStep): ?string
    {
        $steps = array_keys($this->steps());
        $index = array_search($currentStep, $steps);

        return $steps[$index + 1] ?? null;
    }

    public function getSitePreferenceFields(): FieldCollection
    {
        return Preference::allFieldsIn('default')
            ->whereIn('name', [
                'fields.default::general->site_name',
                'fields.default::general->site_url',
            ])
            ->loadMetafields()
            ->values();
    }

    public function getThemePreferenceFields(): FieldCollection
    {
        $installmentPreferenceKeys = theme()->installmentPreferenceKeys();
        $allFields = Preference::allFieldsIn(theme()->namespace());

        $fields = new FieldCollection();

        foreach ($installmentPreferenceKeys as $key) {
            $fieldKey = explode('.', $key, 2)[1] ?? null;

            if ($fieldKey && ($field = $allFields->findByKey($fieldKey))) {
                $fields->push($field);
            }
        }

        return $fields->loadMetafields();
    }

    public function handleSiteStep(Request $request): void
    {
        $fields = $this->getSitePreferenceFields();

        $this->savePreferences($request, $fields);
    }

    public function handleThemeStep(Request $request): void
    {
        $fields = Preference::allFieldsIn(theme()->namespace());

        $this->savePreferences($request, $fields);
    }

    public function handleVariantStep(Request $request): void
    {
        $variants = theme()->getVariants();

        $validated = $request->validate([
            'variant' => ['required', 'string', new In($variants->pluck('name'))],
        ], [], ['variant' => __('Variant')]);

        $selectedVariant = $variants->firstWhere('name', $validated['variant']);

        if (is_null($selectedVariant)) {
            return;
        }

        $selectedVariant->handle();
    }

    private function savePreferences(Request $request, Collection $fields): void
    {
        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);
        /** @var FieldValidationService $fieldValidationService */
        $fieldValidationService = app(FieldValidationService::class);

        $fieldValues = $fieldInputService->getFieldValues($fields, $request->all());
        $rulesAndAttributes = $fieldValidationService->get($fields, $fieldValues);

        $request->validate($rulesAndAttributes['rules'], [], $rulesAndAttributes['attributes']);

        $validatedFieldValues = $fieldInputService->getDottedFieldValues($fieldValues);

        preference($validatedFieldValues);
    }
}
