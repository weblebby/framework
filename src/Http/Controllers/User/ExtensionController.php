<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExtensionController extends Controller
{
    public function index(): View
    {
        $this->authorize('extension:read');

        seo()->title(t('Yüklü Eklentiler', 'admin'));

        return view('feadmin::user.extensions.index');
    }

    public function enable(string $id): RedirectResponse
    {
        $this->authorize('extension:update');

        extensions()
            ->all()
            ->where('id', $id)
            ->firstOrFail()
            ->enable();

        return back()->with('message', t('Eklenti aktifleştirildi', 'admin'));
    }

    public function disable(string $id): RedirectResponse
    {
        $this->authorize('extension:update');

        extensions()
            ->all()
            ->where('id', $id)
            ->firstOrFail()
            ->disable();

        return back()->with('message', t('Eklenti devre dışı bırakıldı', 'admin'));;
    }
}
