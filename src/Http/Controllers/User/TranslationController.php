<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreTranslationRequest;
use Feadmin\Services\TranslationFinderService;
use Illuminate\Http\JsonResponse;

class TranslationController extends Controller
{
    public function store(StoreTranslationRequest $request, TranslationFinderService $service): JsonResponse
    {
        $service->updateTranslation($request->code, $request->key, $request->value);

        return response()->json(['message' => __('Çeviri kaydedildi')]);
    }
}
