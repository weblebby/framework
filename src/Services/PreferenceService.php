<?php

namespace Feadmin\Services;

use Feadmin\Hooks\Preference;
use Feadmin\Models\Preference as PreferenceModel;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class PreferenceService
{
    private Collection $preferences;

    private Preference $hook;

    public function __construct()
    {
        $this->hook = new Preference();
        $this->preferences = PreferenceModel::withTranslation()->get();
    }

    public function hook(): Preference
    {
        return $this->hook;
    }

    public function get(string $rawKey, mixed $default = null): mixed
    {
        [$finded, $namespace, $bag, $key] = $this->find($rawKey);


        $field = $this->hook()->field($namespace, $bag, $key);
        $value = $finded->value ?? ($field['default'] ?? $default);

        return match ($field['type'] ?? null) {
            'image' => $finded?->getFirstMediaUrl(conversionName: 'lg') ?? '',
            'richtext' => new HtmlString($value),
            default => $value,
        };
    }

    public function set(array $data): array
    {
        $saved = [];

        foreach ($data as $rawKey => $value) {
            [$finded, $namespace, $bag, $key] = $this->find($rawKey);

            $field = $this->hook()->field($namespace, $bag, $key);
            $valueless = $finded && $field['type'] === 'image';

            if (is_null($finded) && filled($value)) {
                $saved[] = PreferenceModel::create(array_filter([
                    'namespace' => $namespace,
                    'bag' => $bag,
                    'key' => $key,
                    'value' => $valueless ? null : $value,
                ]));

                continue;
            }

            if ($valueless) {
                $saved[] = $finded;
                continue;
            }

            if ($finded && blank($value)) {
                $finded->delete();
                continue;
            }

            if ($finded) {
                $finded->update(['value' => $value]);
                $saved[] = $finded;
            }
        }

        return $saved;
    }

    private function find(string $rawKey): array
    {
        if (!str_contains($rawKey, '::')) {
            $rawKey = "default::{$rawKey}";
        }

        [$namespace, $bagAndKey] = explode('::', $rawKey);
        [$bag, $key] = explode('->', $bagAndKey, 2);

        $findedPreference = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->where('key', $key)
            ->first();

        return [$findedPreference, $namespace, $bag, $key];
    }
}
