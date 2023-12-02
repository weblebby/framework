<?php

use Feadmin\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('test', function () {
    /** @var \Feadmin\Models\Page $page */
    $page = \Feadmin\Models\Page::first();

    dd($page->getAbilities());
});

/**
 * Post
 */
Route::get(sprintf('%s/{post}', preference('slugs->post')), [Controllers\PostController::class, 'show'])
    ->name('posts.show');
