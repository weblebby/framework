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
        $postable = PostModels::find($request->input('type', Post::getModelName()));
        abort_if(is_null($postable), 404);

        $this->authorize($postable::getPostAbilityFor('read'));

        $posts = $postable::query()
            ->withTaxonomies()
            ->search($request->only(['term', 'status']))
            ->paginate();

        seo()->title($postable::getPluralName());

        return view('feadmin::user.posts.index', compact('posts', 'postable'));
    }

    public function create(Request $request): View
    {
        $postable = PostModels::find($request->input('type', Post::getModelName()));
        abort_if(is_null($postable), 404);

        $this->authorize($postable::getPostAbilityFor('create'));

        $posts = $postable::query()->paginate();
        $templates = Theme::active()->templatesFor($postable::class);
        $sections = $postable::getPostSections()->toArray();

        if (old('template')) {
            $sections = array_merge($sections, $templates->firstWhere('name', old('template'))->sections()->toArray());
        }

        $categories = ($categoryTaxonomy = $postable::getTaxonomyFor('category'))
            ? Taxonomy::query()
                ->taxonomy($categoryTaxonomy->name())
                ->with('term')
                ->onlyParents()
                ->withRecursiveChildren()
                ->get()
            : null;

        seo()->title(__(':name oluştur', ['name' => $postable::getSingularName()]));

        return view('feadmin::user.posts.create', compact(
            'posts',
            'templates',
            'sections',
            'categories',
            'postable'
        ));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        dd($request->validated());
        $request->postable::query()->create($request->validated());

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
