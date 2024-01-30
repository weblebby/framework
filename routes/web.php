<?php

use Illuminate\Support\Facades\Route;
use Weblebby\Framework\Http\Controllers;

/**
 * Post
 */
Route::get(sprintf('%s/{post}', preference('slugs->post')), [Controllers\PostController::class, 'show'])
    ->name('posts.show');
