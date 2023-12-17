<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Theme;
use Feadmin\Http\Requests\User\StorePostRequest;
use Feadmin\Http\Requests\User\UpdatePostRequest;
use Feadmin\Models\Post;
use Feadmin\Models\Taxonomy;
use Feadmin\Services\TaxonomyService;
use Feadmin\Services\User\PostFieldService;
use Feadmin\Services\User\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

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
            'templates',
            'sections',
            'categories',
            'postable',
        ));
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(
        StorePostRequest $request,
        PostService      $postService,
        PostFieldService $postFieldService,
    ): RedirectResponse
    {
        /** @var Post $postable */
        $postable = $request->postable::query()->create($request->validated());

        $metafields = $postFieldService->dottedMetafieldValues($request->postable, $request->validated());
        $postable->setMetafieldWithSchema($metafields);

        $postService->attachTaxonomies($postable, $request->safe()->offsetGet('taxonomies') ?? []);

        return to_panel_route('posts.index', ['type' => $request->postable::getModelName()])
            ->with('message', __('Yazı oluşturuldu'));
    }

    public function edit(Post $post): View
    {
        /** @var PostInterface $postable */
        $postable = $post->type::findOrFail($post->getKey());
        $this->authorize($postable::getPostAbilityFor('update'));

        $templates = Theme::active()->templatesFor($postable::class);

        $sections = $postable::getPostSections()
            ->withTemplateSections($postable, old('template'))
            ->toArray();

        dd($postable->getMetafieldValues());

        $categories = ($categoryTaxonomy = $postable::getTaxonomyFor('category'))
            ? Taxonomy::query()
                ->taxonomy($categoryTaxonomy->name())
                ->with('term')
                ->onlyParents()
                ->withRecursiveChildren()
                ->get()
            : null;

        seo()->title(__('Yazıyı [:post] düzenle', ['post' => $post->title]));

        return view('feadmin::user.posts.edit', compact(
            'post',
            'templates',
            'sections',
            'categories',
            'postable',
        ));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return to_panel_route('posts.index')->with('message', __('Yazı güncellendi'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        /** @var PostInterface $postable */
        $postable = $post->type::findOrFail($post->getKey());
        $this->authorize($postable::getPostAbilityFor('delete'));

        $postable->delete();

        return to_panel_route('posts.index', ['type' => $postable::getModelName()])
            ->with('message', __(':post silindi', ['post' => $postable::getSingularName()]));
    }
}
