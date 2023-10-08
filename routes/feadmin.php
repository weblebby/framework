<?php

use Feadmin\Features;
use Feadmin\Hooks\Panel;
use Feadmin\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

/** @var Panel $panel */

/**
 * Dashboard
 */
Route::get('/', [User\DashboardController::class, 'index'])->name('dashboard');

/**
 * Navigations
 */
if (Features::enabled(Features::navigations(), $panel->name())) {
    Route::post(
        'navigations/{navigation}/sort',
        [User\NavigationController::class, 'sort']
    )->name('navigations.sort');

    Route::resource(
        'navigations.items',
        User\NavigationItemController::class
    )->only('store', 'update', 'destroy');

    Route::resource(
        'navigations',
        User\NavigationController::class
    )->except(['create', 'edit']);
}

/**
 * Extensions
 */
if (Features::enabled(Features::extensions(), $panel->name())) {
    Route::controller(User\ExtensionController::class)
        ->prefix('extensions/{extensions}')
        ->name('extensions.')
        ->group(function () {
            Route::put('enable', 'enable')->name('enable');
            Route::put('disable', 'disable')->name('disable');
        });

    Route::resource('extensions.preferences', User\ExtensionPreferenceController::class);
    Route::resource('extensions', User\ExtensionController::class);
}

/**
 * Locales
 */
if (Features::enabled(Features::translations(), $panel->name())) {
    Route::post('translations', [User\TranslationController::class, 'store'])
        ->name('translations.store');

    Route::post('locales/sync', [User\LocaleController::class, 'sync'])->name('locales.sync');
    Route::resource('locales', User\LocaleController::class);
}

/**
 * Preferences
 */
if (Features::enabled(Features::preferences(), $panel->name())) {
    Route::prefix('preferences')
        ->name('preferences.')
        ->controller(User\PreferenceController::class)
        ->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('{bag}', 'show')->name('show');
            Route::put('{namespace}/{bag}', 'update')->name('update');
        });
}

/**
 * Roles
 */
if (Features::enabled(Features::roles(), $panel->name())) {
    Route::resource('roles', User\RoleController::class)->except('show');
}

/**
 * Users
 */
if (Features::enabled(Features::users(), $panel->name())) {
    Route::resource('users', User\UserController::class);
}
