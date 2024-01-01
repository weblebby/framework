<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\Theme;
use Feadmin\Http\Requests\User\StorePostRequest;
use Feadmin\Http\Requests\User\UpdatePostRequest;
use Feadmin\Items\Field\CodeEditorFieldItem;
use Feadmin\Items\Field\Contracts\FieldInterface;
use Feadmin\Models\Metafield;
use Feadmin\Models\Post;
use Feadmin\Models\Taxonomy;
use Feadmin\Services\FieldInputService;
use Feadmin\Services\TaxonomyService;
use Feadmin\Services\User\PostFieldService;
use Feadmin\Services\User\PostService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

    public function create(Request $request, PostService $postService): View
    {
        $postable = PostModels::findOrFail($request->input('type', Post::getModelName()));
        $this->authorize($postable::getPostAbilityFor('create'));

        $templates = $postService->templates($postable);
        $sections = $postService->sections($postable, old('template'));
        $categories = $postService->taxonomies($postable, 'category');

        $isCodeEditorNeeded = $sections->allFields()->hasAnyTypeOf(CodeEditorFieldItem::class);

        seo()->title(__(':name oluştur', ['name' => $postable::getSingularName()]));

        $sections = $sections->toArray();
        
        return view('feadmin::user.posts.create', compact(
            'templates',
            'sections',
            'categories',
            'postable',
            'isCodeEditorNeeded',
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
        /** @var Post $post */
        $post = $request->postable::query()->create($validated = $request->validated());

        /**
         * This order is important for metafields to be updated correctly.
         */
        $post->setMetafieldWithSchema($postFieldService->dottedMetafieldValues($post, $request->validated()));
        $post->reorderMetafields($validated['_reordered_fields'] ?? []);
        $post->resetMetafieldKeys();

        $postService->syncTaxonomies(
            postable: $post,
            taxonomies: $request->safe()->offsetGet('taxonomies') ?? [],
            primaryTaxonomyId: $request->input('primary_category'),
        );

        if ($validated['featured_image'] ?? null) {
            $post->addMedia($validated['featured_image'])->toMediaCollection('featured');
        }

        return to_panel_route('posts.edit', $post)
            ->with('message', __('Yazı oluşturuldu'));
    }

    public function edit(Post $post, PostService $postService): View
    {
        $this->authorize($post::getPostAbilityFor('update'));

        $templates = $postService->templates($post);
        $sections = $postService->sections($post, old('template', $post->template));
        $categories = $postService->taxonomies($post, 'category');
        $metafields = $post->getMetafieldValues();
        $metafields['slug'] = $post->slug;

        $isCodeEditorNeeded = $sections->allFields()->hasAnyTypeOf(CodeEditorFieldItem::class);

        seo()->title(__('Yazıyı [:post] düzenle', ['post' => $post->title]));

        $sections = $sections->toArray();

        return view('feadmin::user.posts.edit', compact(
            'post',
            'templates',
            'sections',
            'categories',
            'post',
            'metafields',
            'isCodeEditorNeeded',
        ));
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(
        StorePostRequest $request,
        PostService      $postService,
        PostFieldService $postFieldService,
        Post             $post
    ): RedirectResponse
    {
        $post->update($validated = $request->validated());

        /**
         * This order is important for metafields to be updated correctly.
         */
        $post->deleteMetafields(startsWith: $validated['_deleted_fields'] ?? []);
        $post->setMetafieldWithSchema($postFieldService->dottedMetafieldValues($post, $request->validated()));
        $post->reorderMetafields($validated['_reordered_fields'] ?? []);
        $post->resetMetafieldKeys();

        $postService->syncTaxonomies(
            postable: $post,
            taxonomies: $request->safe()->offsetGet('taxonomies') ?? [],
            primaryTaxonomyId: $request->input('primary_category'),
        );

        if ($validated['featured_image'] ?? null) {
            $post->addMedia($validated['featured_image'])->toMediaCollection('featured');
        }

        return to_panel_route('posts.edit', $post)
            ->with('message', __('Yazı :post güncellendi', ['post' => $post->title]));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize($post::getPostAbilityFor('delete'));

        $post->delete();

        return to_panel_route('posts.index', ['type' => $post::getModelName()])
            ->with('message', __(':post silindi', ['post' => $post::getSingularName()]));
    }
}
