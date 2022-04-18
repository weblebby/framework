<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreTranslationRequest;
use Feadmin\Services\TranslationFinderService;

class TranslationController extends Controller
{
    public function store(StoreTranslationRequest $request, TranslationFinderService $service)
    {
        $service->updateTranslation($request->code, $request->key, $request->value);

        return response()->json(['message' => t('Çeviri kaydedildi', 'panel')]);
    }
}
