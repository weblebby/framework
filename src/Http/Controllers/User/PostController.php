<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Facades\Extension;
use Feadmin\Facades\PostModels;
use Feadmin\Http\Requests\User\StorePostRequest;
use Feadmin\Items\Field\CodeEditorFieldItem;
use Feadmin\Models\Post;
use Feadmin\Services\User\PostFieldService;
use Feadmin\Services\User\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        $isTranslatable = Extension::has('multilingual');

        seo()->title(__(':name oluştur', ['name' => $postable::getSingularName()]));

        $sections = $sections->toArray();

        return view('feadmin::user.posts.create', compact(
            'templates',
            'sections',
            'categories',
            'postable',
            'isCodeEditorNeeded',
            'isTranslatable'
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
        $validated = $request->validated();
        $locale = request('_locale', app()->getLocale());

        /** @var Post $post */
        $post = $request->postable::query()->create([
            ...Arr::except($validated, $request->postable->translatedAttributes),
            $locale => [
                ...Arr::only($validated, $request->postable->translatedAttributes),
                'slug' => $validated['fields']['slug'],
            ],
        ]);

        unset($validated['fields']['slug']);

        /**
         * This order is important for metafields to be updated correctly.
         */
        $post->setMetafieldWithSchema(
            $postFieldService->dottedMetafieldValues($post, $validated),
            locale: $locale,
        );
        $post->reorderMetafields($validated['_reordered_fields'] ?? []);
        $post->resetMetafieldKeys();

        $postService->syncTaxonomies(
            postable: $post,
            taxonomies: $request->safe()->offsetGet('taxonomies') ?? [],
            primaryTaxonomyId: $request->input('primary_category'),
            locale: $locale,
        );

        if ($validated['featured_image'] ?? null) {
            $post->addMedia($validated['featured_image'])->toMediaCollection('featured');
        }

        return to_panel_route('posts.edit', [$post, 'locale' => $locale])
            ->with('message', __('Yazı oluşturuldu'));
    }

    public function edit(Request $request, Post $post, PostService $postService): View
    {
        $this->authorize($post::getPostAbilityFor('update'));

        $locale = $request->input('_locale');
        $translatedPost = $post->translate($locale, withFallback: true);

        $templates = $postService->templates($post);
        $sections = $postService->sections($post, old('template', $post->template));
        $categories = $postService->taxonomies($post, 'category');
        $metafields = $post->getMetafieldValues(locale: $locale);
        $metafields['slug'] = $translatedPost->slug;

        $isCodeEditorNeeded = $sections->allFields()->hasAnyTypeOf(CodeEditorFieldItem::class);
        $isTranslatable = Extension::has('multilingual');

        seo()->title(__('Yazıyı [:post] düzenle', ['post' => $post->title]));

        $sections = $sections->toArray();

        return view('feadmin::user.posts.edit', compact(
            'post',
            'translatedPost',
            'templates',
            'sections',
            'categories',
            'post',
            'metafields',
            'isCodeEditorNeeded',
            'isTranslatable'
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
        $validated = $request->validated();
        $locale = request('_locale', app()->getLocale());

        $post->update([
            ...Arr::except($validated, $request->postable->translatedAttributes),
            $locale => [
                ...Arr::only($validated, $request->postable->translatedAttributes),
                'slug' => $validated['fields']['slug'],
            ],
        ]);

        unset($validated['fields']['slug']);

        /**
         * This order is important for metafields to be updated correctly.
         */
        $post->deleteMetafields(startsWith: $validated['_deleted_fields'] ?? []);
        $post->setMetafieldWithSchema(
            $postFieldService->dottedMetafieldValues($post, $validated),
            locale: $locale,
        );
        $post->reorderMetafields($validated['_reordered_fields'] ?? []);
        $post->resetMetafieldKeys();

        $postService->syncTaxonomies(
            postable: $post,
            taxonomies: $request->safe()->offsetGet('taxonomies') ?? [],
            primaryTaxonomyId: $request->input('primary_category'),
            locale: $locale,
        );

        if ($validated['featured_image'] ?? null) {
            $post->addMedia($validated['featured_image'])->toMediaCollection('featured');
        }

        return to_panel_route('posts.edit', [$post, 'locale' => $locale])
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
