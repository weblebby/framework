<?php

namespace Feadmin\Services\User;

use Feadmin\Facades\Theme;
use Feadmin\Abstracts\Theme\Theme as ThemeAbstract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class AppearanceEditorService
{
    protected ThemeAbstract $theme;

    public function __construct()
    {
        $this->theme = Theme::active();
    }

    public function files(): array
    {
        $finder = (new Finder())
            ->in($this->onlyExistingDirectories([
                $this->theme->path('resources/views'),
                $this->theme->vendorPath()
            ]))
            ->ignoreVCSIgnored(true);

        return collect([...$finder->files()->getIterator()])
            ->unique(fn(SplFileInfo $file) => $file->getRelativePathname())
            ->groupBy(fn(SplFileInfo $file) => str_replace('/', '.', $file->getRelativePath()) . '.')
            ->sortByDesc(fn($_, string $path) => $path)
            ->undot()
            ->toArray();
    }

    public function getFile(string $file): ?SplFileInfo
    {
        $path = $this->theme->vendorPath($file);

        if (!file_exists($path)) {
            $path = $this->theme->path("resources/views/{$file}");
        }

        if (!file_exists($path)) {
            return null;
        }

        return new SplFileInfo($path, '', $file);
    }

    public function updateFile(string $file, string $content): void
    {
        File::ensureDirectoryExists($this->theme->vendorPath(dirname($file)));

        file_put_contents($this->theme->vendorPath($file), $content);
    }

    public function onlyExistingDirectories(array $directories): array
    {
        return array_filter($directories, fn(string $directory) => file_exists($directory));
    }
}