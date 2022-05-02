<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Extension;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExtensionController extends Controller
{
    public function index(): View
    {
        $this->authorize('extension:read');

        seo()->title(__('Yüklü Eklentiler'));

        return view('feadmin::user.extensions.index');
    }

    public function enable(string $id): RedirectResponse
    {
        $this->authorize('extension:update');

        Extension::get()->firstOrFail('id', $id)->enable();

        return back()->with('message', __('Eklenti aktifleştirildi'));
    }

    public function disable(string $id): RedirectResponse
    {
        $this->authorize('extension:update');

        Extension::get()->firstOrFail('id', $id)->disable();

        return back()->with('message', __('Eklenti devre dışı bırakıldı'));;
    }
}
