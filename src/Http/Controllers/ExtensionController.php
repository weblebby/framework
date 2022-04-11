<?php

namespace Feadmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Extension;
use Illuminate\Support\Facades\File;

class ExtensionController extends Controller
{
    public function asset(string $id, string $asset)
    {
        $extension = Extension::enabled()->firstOrFail('id', $id);

        if (!file_exists($path = $extension->path("Public/{$asset}"))) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => match (File::extension($path)) {
                'css' => 'text/css',
                'js' => 'application/javascript',
                default => mime_content_type($path),
            },
        ]);
    }
}
