<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Http\Requests\User\StorePostRequest;
use Weblebby\Framework\Items\Field\CodeEditorFieldItem;
use Weblebby\Framework\Models\Post;
use Weblebby\Framework\Services\User\PostFieldService;
use Weblebby\Framework\Services\User\PostService;

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

        return view('weblebby::user.posts.index', compact('posts', 'postable'));
    }

    public function create(Request $request, PostService $postService): View
    {
        $postable = PostModels::findOrFail($request->input('type', Post::getModelName()));
        $this->authorize($postable::getPostAbilityFor('create'));

        $locale = $request->input('_locale', app()->getLocale());

        $templates = $postService->templates($postable);
        $sections = $postService->sections($postable, old('template'));
        $categories = $postService->taxonomies($postable, taxonomy: 'category', locale: $locale);

        $isCodeEditorNeeded = $sections->allFields()->hasAnyTypeOf(CodeEditorFieldItem::class);
        $isTranslatable = Extension::has('multilingual');

        seo()->title(__('Create :title', ['title' => $postable::getSingularName()]));

        $sections = $sections->toArray();

        return view('weblebby::user.posts.create', compact(
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
        PostService $postService,
        PostFieldService $postFieldService,
    ): RedirectResponse {
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
            ->with('message', __(':title created', ['title' => $post::getSingularName()]));
    }

    public function edit(Request $request, Post $post, PostService $postService): View
    {
        $this->authorize($post::getPostAbilityFor('update'));

        $locale = $request->input('_locale', app()->getLocale());
        $translatedPost = $post->translate($locale, withFallback: true);

        $templates = $postService->templates($post);
        $sections = $postService->sections($post, old('template', $post->template));
        $categories = $postService->taxonomies($post, taxonomy: 'category', locale: $locale);
        $metafields = $post->getMetafieldValues(locale: $locale, options: [
            'media_as_url' => true,
        ]);
        $metafields['slug'] = $translatedPost->slug;

        $isCodeEditorNeeded = $sections->allFields()->hasAnyTypeOf(CodeEditorFieldItem::class);
        $isTranslatable = Extension::has('multilingual');

        seo()->title(__('Edit (:title)', ['title' => $post->title]));

        $sections = $sections->toArray();

        return view('weblebby::user.posts.edit', compact(
            'post',
            'translatedPost',
            'locale',
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
        PostService $postService,
        PostFieldService $postFieldService,
        Post $post
    ): RedirectResponse {
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

        $dottedMetafields = $postFieldService->dottedMetafieldValues($post, $validated);

        /**
         * This order is important for metafields to be updated correctly.
         */
        $post->deleteMetafields(startsWith: $validated['_deleted_fields'] ?? []);
        $post->setMetafieldWithSchema($dottedMetafields, locale: $locale);
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
            ->with('message', __(':title updated', ['title' => $post->title]));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize($post::getPostAbilityFor('delete'));

        $post->delete();

        return to_panel_route('posts.index', ['type' => $post::getModelName()])
            ->with('message', __(':title deleted', ['title' => $post::getSingularName()]));
    }
}
