<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Weblebby\Framework\Facades\Extension;

class ExtensionController extends Controller
{
    public function index(): View
    {
        $this->authorize('extension:read');

        seo()->title(__('Extensions'));

        return view('weblebby::user.extensions.index');
    }

    public function enable(string $name): RedirectResponse
    {
        $this->authorize('extension:activate');

        Extension::findByNameOrFail($name)->activate();

        return back()->with('message', __('Extension activated'));
    }

    public function disable(string $name): RedirectResponse
    {
        $this->authorize('extension:deactivate');

        Extension::findByNameOrFail($name)->deactivate();

        return back()->with('message', __('Extension deactivated'));
    }
}
