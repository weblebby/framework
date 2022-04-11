<?php

use Feadmin\Http\Controllers\ExtensionController;
use Illuminate\Support\Facades\Route;

/**
 * Extension Asset
 */
Route::get('ext-asset/{extension}/{asset}', [ExtensionController::class, 'asset'])
    ->where('asset', '.*')
    ->name('ext-asset');
