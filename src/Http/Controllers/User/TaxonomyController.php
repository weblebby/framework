<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Theme;
use Feadmin\Http\Requests\User\StorePostRequest;
use Feadmin\Http\Requests\User\UpdatePostRequest;
use Feadmin\Models\Post;
use Feadmin\Models\Taxonomy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(Request $request): View
    {
        $taxonomyOption = PostModels::taxonomy($request->taxonomy);
        abort_if(is_null($taxonomyOption), 404);

        $this->authorize($taxonomyOption->abilityFor('read'));

        $taxonomies = Taxonomy::query()
            ->taxonomy($taxonomyOption->name())
            ->with('term:id,title')
            ->paginate();

        return view('feadmin::user.taxonomies.index', compact(
            'taxonomies',
            'taxonomyOption'
        ));
    }

    public function create(): View
    {
        $this->authorize('post:create');

        $posts = Post::query()->paginate();
        $templates = Theme::active()->templatesFor(Post::class);

        $taxonomies = Taxonomy::query()
            ->taxonomy(Post::class)
            ->with('term')
            ->onlyParents()
            ->withRecursiveChildren()
            ->get();

        seo()->title(__('Yazı oluştur'));

        return view('feadmin::user.posts.create', [
            ...compact('posts', 'templates', 'taxonomies'),
            'postType' => Post::class,
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        Post::query()->create($request->validated());

        return to_panel_route('posts.index')->with('message', __('Yazı oluşturuldu'));
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
