<?php

namespace Feadmin\Http\Controllers\User;

use Core\Facades\Localization;
use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreTranslationRequest;
use Illuminate\Support\Facades\DB;

class TranslationController extends Controller
{
    public function store(StoreTranslationRequest $request)
    {
        $localeId = Localization::getLocale($request->code)->id;

        DB::table('locale_translations')->updateOrInsert(
            ['locale_id' => $localeId, 'group' => $request->group, 'key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json(['message' => t('Çeviri kaydedildi', 'admin')]);
    }
}
