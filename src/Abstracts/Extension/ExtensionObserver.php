<?php

namespace Feadmin\Abstracts\Extension;

use Illuminate\Support\Facades\File;

abstract class ExtensionObserver
{
    protected Extension $extension;

    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }

    public function activated(): void
    {
        //
    }

    public function deactivated(): void
    {
        //
    }

    protected function publishBuildAssets(): void
    {
        $sourcePath = $this->extension->path('public');
        $targetPath = public_path("extensions/{$this->extension->name()}");

        // create extensions directory if not exists
        File::ensureDirectoryExists(public_path('extensions'));

        if (File::exists($targetPath)) {
            // delete symlink
            unlink($targetPath);
        }

        symlink($sourcePath, $targetPath);
    }
}
