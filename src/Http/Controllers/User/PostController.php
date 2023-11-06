<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StorePostRequest;
use Feadmin\Http\Requests\User\UpdatePostRequest;
use Feadmin\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $this->authorize('post:read');
        
        $posts = Post::query()->paginate();

        seo()->title(__('Yazılar'));

        return view('feadmin::user.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $this->authorize('post:create');

        $posts = Post::query()->paginate();

        seo()->title(__('Yazı oluştur'));

        return view('feadmin::user.posts.create', compact('posts'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        Post::query()->create($request->validated());

        return to_panel_route('posts.index')->with('success', __('Yazı oluşturuldu'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('post:update');

        $posts = Post::query()->paginate();

        seo()->title(__('Yazıyı [:post] düzenle', ['post' => $post->title]));

        return view('feadmin::user.posts.edit', compact('post', 'posts'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return to_panel_route('posts.index')->with('message', __('Yazı güncellendi'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('post:delete');

        $post->delete();

        return to_panel_route('users.index')->with('message', __('Yazı silindi'));
    }
}
