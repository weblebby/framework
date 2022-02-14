<?php

namespace Feadmin\Services;

use Feadmin\Facades\Localization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Finder;

class TranslationFinderService
{
    private array $filePatterns = [
        '*.php',
        '*.js',
    ];

    private array $functions = [
        '@t',
        't',
    ];

    private string $functionPattern = '/\b[[FUNCTIONS]]\(\s*[\'|"](.+)[\'|"],\s*[\'|"](.+)[\'|"]/Um';

    public function scan(): void
    {
        $current = $this->getTranslations();
        $prev = Localization::getTranslations();

        $unusedTranslationIds = $this
            ->getUnusedTranslations($current, $prev)
            ->pluck('id')
            ->toArray();

        DB::table('locale_translations')->delete($unusedTranslationIds);

        $current->each(function ($translation) {
            $this->saveToDatabase(...$translation);
        });
    }

    private function getUnusedTranslations(Collection $current, Collection $prev): Collection
    {
        $unusedTranslations = collect();

        foreach ($prev as $translation) {
            $isEmpty = $current
                ->where('key', $translation->key)
                ->where('group', $translation->group)
                ->isEmpty();

            if ($isEmpty) {
                $unusedTranslations[] = $translation;
            }
        }

        return $unusedTranslations;
    }

    private function getTranslations(): Collection
    {
        $files = $this->getFiles();
        $functionPattern = $this->getFunctionPattern();

        $translations = collect();

        foreach ($files as $file) {
            preg_match_all($functionPattern, $file->getContents(), $matches);

            if (count($matches[1]) <= 0) {
                continue;
            }

            for ($i = 0; $i < count($matches[1]); $i++) {
                $translations[] = [
                    stripslashes($matches[1][$i]),
                    $matches[2][$i],
                ];
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

    private function saveToDatabase(string $key, string $group): bool
    {
        $defaultLocaleId = Localization::getDefaultLocaleId();

        $exists = DB::table('locale_translations')
            ->where('locale_id', $defaultLocaleId)
            ->where('key', $key)
            ->where('group', $group)
            ->exists();

        if ($exists) {
            return false;
        }

        return DB::table('locale_translations')->insert([
            'locale_id' => $defaultLocaleId,
            'group' => $group,
            'key' => $key,
            'value' => $key,
        ]);
    }
}
