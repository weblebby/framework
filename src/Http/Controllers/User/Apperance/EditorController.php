<?php

namespace Feadmin\Http\Controllers\User\Apperance;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\UpdateAppearanceEditorRequest;
use Feadmin\Services\User\AppearanceEditorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditorController extends Controller
{
    public function index(Request $request, AppearanceEditorService $appearanceEditorService): View
    {
        $this->authorize('appearance:editor:read');

        $files = $appearanceEditorService->files();
        $filePath = $request->input('file', 'home.blade.php');
        $requestedFile = $appearanceEditorService->getFile($filePath);

        if (is_null($requestedFile)) {
            abort(404);
        }

        seo()->title(__(':path - Tema Editörü', ['path' => $filePath]));

        return view('feadmin::user.appearance.editor.index', compact(
            'files',
            'filePath',
            'requestedFile'
        ));
    }

    public function update(
        UpdateAppearanceEditorRequest $request,
        AppearanceEditorService       $appearanceEditorService
    ): RedirectResponse
    {
        $appearanceEditorService->updateFile(
            $request->input('file'),
            $request->input('content')
        );

        return back()->with('message', __('Dosya başarıyla güncellendi.'));
    }
}