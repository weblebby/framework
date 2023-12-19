<?php

use Feadmin\Http\Controllers;
use Illuminate\Support\Facades\Route;

/**
 * Post
 */
Route::get(sprintf('%s/{post}', preference('slugs->post')), [Controllers\PostController::class, 'show'])
    ->name('posts.show');
