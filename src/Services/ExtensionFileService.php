<?php

namespace Weblebby\Framework\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Weblebby\Framework\Abstracts\Extension\Extension as ExtensionAbstract;
use Weblebby\Framework\Facades\Extension;

class ExtensionFileService
{
    public function isExtensionActive(ExtensionAbstract $extension): bool
    {
        $extensions = $this->getExtensionsFromFile();

        return $extensions->firstWhere('name', $extension->name())['is_active'] ?? false;
    }

    public function activateExtension(ExtensionAbstract $extension): void
    {
        $extension->activate();
        $this->saveExtensionsToFile();
    }

    public function deactivateExtension(ExtensionAbstract $extension): void
    {
        $extension->deactivate();
        $this->saveExtensionsToFile();
    }

    protected function getExtensionFilePath(): string
    {
        return storage_path('extensions.json');
    }

    protected function saveExtensionsToFile(): void
    {
        $extensions = Extension::getWithDeactivated()->map(function (ExtensionAbstract $extension) {
            return [
                'name' => $extension->name(),
                'is_active' => $extension->isActive(),
            ];
        });

        File::put($this->getExtensionFilePath(), $extensions->toJson(JSON_PRETTY_PRINT));
    }

    /**
     * @return Collection<int, ExtensionAbstract>
     */
    protected function getExtensionsFromFile(): Collection
    {
        try {
            return collect(json_decode(File::get($this->getExtensionFilePath()), true));
        } catch (FileNotFoundException) {
            return collect();
        }
    }
}
