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

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $model = PostModels::find($request->input('type', Post::getModelName()));
        abort_if(is_null($model), 404);

        $this->authorize($model::getPostAbilityFor('read'));

        $posts = $model::query()
            ->withTaxonomies()
            ->search($request->only(['term', 'status']))
            ->paginate();

        seo()->title($model::getPluralName());

        return view('feadmin::user.posts.index', compact('posts', 'model'));
    }

    public function create(Request $request): View
    {
        $model = PostModels::find($request->input('type', Post::getModelName()));
        abort_if(is_null($model), 404);

        $this->authorize($model::getPostAbilityFor('create'));

        $posts = $model::query()->paginate();
        $templates = Theme::active()->templatesFor($model::class);
        $sections = $model::getPostSections()->toArray();

        $taxonomies = Taxonomy::query()
            ->taxonomy($model::getTaxonomyFor('category'))
            ->with('term')
            ->onlyParents()
            ->withRecursiveChildren()
            ->get();

        seo()->title(__(':name oluştur', ['name' => $model::getSingularName()]));

        return view('feadmin::user.posts.create', compact(
            'posts',
            'templates',
            'sections',
            'taxonomies',
            'model'
        ));
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
