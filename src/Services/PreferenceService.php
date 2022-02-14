<?php

namespace Feadmin\Services;

use Feadmin\Facades\Feadmin;
use Feadmin\Hooks\PreferenceHook;
use Feadmin\Models\Preference;
use Illuminate\Support\HtmlString;

class PreferenceService
{
    private $preferences;

    private $hook;

    public function __construct()
    {
        $this->hook = new PreferenceHook();
        $this->preferences = Preference::withTranslation()->get();
    }

    public function hook(): PreferenceHook
    {
        return $this->hook;
    }

    public function get(string $rawKey, mixed $default = null): mixed
    {
        [$finded, $namespace, $bag, $key] = $this->find($rawKey);

        $field = Feadmin::currentPanel()->preferences($namespace)->getField($bag, $key);
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

            $field = Feadmin::currentPanel()->preferences($namespace)->getField($bag, $key);
            $valueless = $finded && $field['type'] === 'image';

            if (is_null($finded) && filled($value)) {
                $saved[] = Preference::create(array_filter([
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
        [$namespace, $bagAndKey] = explode('::', $rawKey);
        [$bag, $key] = explode('__', $bagAndKey, 2);

        $findedPreference = $this->preferences
            ->where('namespace', $namespace)
            ->where('bag', $bag)
            ->where('key', $key)
            ->first();

        return [$findedPreference, $namespace, $bag, $key];
    }
}
