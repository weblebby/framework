<?php

namespace Feadmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Extension;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\File;

class ExtensionController extends Controller
{
    public function asset(string $name, string $asset)
    {
        $extension = Extension::findByNameOrFail($name);
        $assetPath = $path = $extension->path("public/{$asset}");

        abort_if(!file_exists($assetPath), 404);

        return response()->file($path, [
            'Content-Type' => MimeType::get(File::extension($path)),
        ]);
    }
}
