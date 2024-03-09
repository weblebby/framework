<?php

use Illuminate\Support\Facades\Route;
use Weblebby\Framework\Facades\Panel;
use Weblebby\Framework\Http\Controllers\User;
use Weblebby\Framework\Items\PanelItem;
use Weblebby\Framework\Support\Features;

/** @var PanelItem $panel */

/**
 * Dashboard
 */
Route::get('/', [User\DashboardController::class, 'index'])->name('dashboard');

/**
 * Navigations
 */
if ($panel->supports(Features::navigations())) {
    Route::resource('navigations.items', User\NavigationItemController::class)
        ->only('store', 'update', 'destroy');

    Route::resource('navigations', User\NavigationController::class)
        ->except(['create', 'edit']);
}

if ($panel === Panel::getMainPanel()) {
    if ($panel->supports(Features::setup())) {
        /**
         * Setup
         */
        Route::get('setup', [User\SetupController::class, 'index'])->name('setup.index');
        Route::put('setup', [User\SetupController::class, 'update'])->name('setup.update');
    }

    /**
     * Extensions
     */
    if ($panel->supports(Features::extensions())) {
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
}

/**
 * Preferences
 */
if ($panel->supports(Features::preferences())) {
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
if ($panel->supports(Features::roles())) {
    Route::resource('roles', User\RoleController::class)->except('show');
}

/**
 * Users
 */
if ($panel->supports(Features::users())) {
    Route::resource('users', User\UserController::class);
}

/**
 * Posts
 */
if ($panel->supports(Features::posts())) {
    Route::resource('posts', User\PostController::class)->except('show');
    Route::resource('taxonomies', User\TaxonomyController::class);
}

/**
 * Appearance
 */
if ($panel->supports(Features::appearance())) {
    Route::get('appearance/editor', [User\Apperance\EditorController::class, 'index'])->name('appearance.editor.index');
    Route::put('appearance/editor', [User\Apperance\EditorController::class, 'update'])->name('appearance.editor.update');
}

/**
 * Themes
 */
if ($panel->supports(Features::themes())) {
    Route::prefix('themes/{theme}')->as('themes.')->group(function () {
        Route::get('preferences/{bag?}', [User\ThemePreferenceController::class, 'index'])->name('preferences.index');

        Route::get('templates/{template}/post-fields', User\ThemeTemplatePostFieldController::class)
            ->name('templates.post-fields');
    });
}
