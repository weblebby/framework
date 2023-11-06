<?php

namespace Feadmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Feadmin\Models\Post;

class PostController extends Controller
{
    public function show(Post $post)
    {
        return $post->title;
    }
}
