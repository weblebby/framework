<?php

use Feadmin\Http\Controllers;
use Illuminate\Support\Facades\Route;

/**
 * Extension Asset
 */
Route::get('ext-asset/{extension}/{asset}', [Controllers\ExtensionController::class, 'asset'])
    ->where('asset', '.*')
    ->name('ext-asset');


/**
 * Post
 */
Route::get(sprintf('%s/{post}', preference('slugs->post')), [Controllers\PostController::class, 'show'])
    ->name('posts.show');
