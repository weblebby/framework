<?php

use Feadmin\Http\Controllers\User\API;
use Illuminate\Support\Facades\Route;

/**
 * Navigation
 */
Route::post('navigations/{navigation}/sort', [API\NavigationSortController::class, 'update'])
    ->name('navigations.sort');

/**
 * Taxonomies
 */
Route::get('taxonomies/{taxonomy}', [API\TaxonomyController::class, 'index'])
    ->name('taxonomies.index');

/**
 * Post Models
 */
Route::get('post-models', [API\PostModelController::class, 'index'])
    ->name('post-models.index');
