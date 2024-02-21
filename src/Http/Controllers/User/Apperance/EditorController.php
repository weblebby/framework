<?php

namespace Weblebby\Framework\Http\Controllers\User\Apperance;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Weblebby\Framework\Http\Requests\User\UpdateAppearanceEditorRequest;
use Weblebby\Framework\Services\User\AppearanceEditorService;

class EditorController extends Controller
{
    public function index(Request $request, AppearanceEditorService $appearanceEditorService): View
    {
        $this->authorize('appearance:editor:read');

        $files = $appearanceEditorService->files();
        $filePath = $request->input('file', 'home.html.twig');
        $requestedFile = $appearanceEditorService->getFile($filePath);

        if (is_null($requestedFile)) {
            abort(404);
        }

        seo()->title(__(':path - Theme Editor', ['path' => $filePath]));

        return view('weblebby::user.appearance.editor.index', compact(
            'files',
            'filePath',
            'requestedFile'
        ));
    }

    public function update(
        UpdateAppearanceEditorRequest $request,
        AppearanceEditorService $appearanceEditorService
    ): RedirectResponse {
        $appearanceEditorService->updateFile(
            $request->input('file'),
            $request->input('content')
        );

        return back()->with('message', __('File updated'));
    }
}
