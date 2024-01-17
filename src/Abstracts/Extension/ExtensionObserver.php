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
}
