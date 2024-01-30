<?php

namespace Weblebby\Framework\Http\Controllers;

use App\Http\Controllers\Controller;
use Weblebby\Framework\Models\Post;

class PostController extends Controller
{
    public function show(Post $post)
    {
        return $post->title;
    }
}
