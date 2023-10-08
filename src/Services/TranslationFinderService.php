<?php

namespace Feadmin\Services;

use Feadmin\Facades\Localization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class TranslationFinderService
{
    private array $filePatterns = [
        '*.php',
        '*.js',
    ];

    private array $functions = [
        '@lang',
        'trans',
        'trans_choice',
        '__',
    ];

    private string $functionPattern = '/([FUNCTIONS])\(\s*([\'"])(?P<string>(?:(?![^\\\]\2).)+.)\2\s*[\),]/u';

    public function scan(): Collection
    {
        $scanned = $this->getScannedTranslations();
        $current = collect(Localization::getTranslations());
        $unusedKeys = $this->getUnusedTranslationKeys($scanned, $current);

        return $current
            ->merge($scanned)
            ->reject(fn ($key) => in_array($key, $unusedKeys));
    }

    public function syncLocale(string $locale, Collection $translations = null): bool
    {
        if (is_null($translations)) {
            $translations = $this->scan();
        }

        return $this->putLocaleFile(
            $locale,
            $translations
                ->map(fn ($_, $key) => __($key, locale: $locale))
                ->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function syncAllLocales(): bool
    {
        $translations = $this->scan();

        foreach (Localization::getSupportedLocales() as $locale) {
            $this->syncLocale($locale->code, $translations);
        }

        return true;
    }

    public function updateTranslation(string $locale, string $key, string $value): bool
    {
        $path = lang_path("{$locale}.json");

        if (!File::exists($path)) {
            return false;
        }

        $json = json_decode(File::get($path), true);

        if (!is_array($json)) {
            return false;
        }

        $json[$key] = $value;

        return $this->putLocaleFile(
            $locale,
            json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function getUnusedTranslationKeys(Collection $scanned, Collection $current): array
    {
        foreach ($current as $key) {
            if (!$scanned->contains($key)) {
                $keys[] = $key;
            }
        }

        return $keys ?? [];
    }

    private function getScannedTranslations(): Collection
    {
        $files = $this->getFiles();
        $functionPattern = $this->getFunctionPattern();

        $translations = collect();

        foreach ($files as $file) {
            preg_match_all($functionPattern, $file->getContents(), $matches);

            if (count($matches[1]) <= 0) {
                continue;
            }

            for ($i = 0; $i < count($matches['string']); $i++) {
                $key = stripslashes($matches['string'][$i]);
                $translations[$key] = $key;
            }
        }

        return $translations;
    }

    private function getDirectories(): array
    {
        return [
            app_path(),
            resource_path(),
            base_path('routes'),
            dirname(__DIR__),
            dirname(__DIR__) . '/../resources',
            dirname(__DIR__) . '/../routes',
        ];
    }

    private function getFiles(): Finder
    {
        $finder = (new Finder())
            ->followLinks()
            ->in($this->getDirectories());

        foreach ($this->filePatterns as $pattern) {
            $finder->name($pattern);
        }

        return $finder->files();
    }

    private function getFunctionPattern(): string
    {
        return str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->functionPattern);
    }

    private function putLocaleFile(string $locale, string $content = '{}'): bool
    {
        if (!File::exists($langPath = lang_path())) {
            File::makeDirectory($langPath, 0755, true);
        }

        return File::put(lang_path("{$locale}.json"), $content, true);
    }

    private function deleteLocaleFile(string $locale): bool
    {
        return File::delete(lang_path("{$locale}.json"));
    }
}
