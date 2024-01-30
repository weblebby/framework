<?php

namespace Weblebby\Framework\Facades;

use Illuminate\Support\Facades\Facade;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Items\TaxonomyItem;
use Weblebby\Framework\Managers\PostModelsManager;

/**
 * @method static void register(string|array $model)
 * @method static array<string, PostInterface> get()
 * @method static PostInterface|null find(string $key)
 * @method static PostInterface findOrFail(string $key)
 * @method static array<string, TaxonomyItem> taxonomies()
 * @method static TaxonomyItem|null taxonomy(string|null $taxonomy)
 *
 * @see PostModelsManager
 */
class PostModels extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PostModelsManager::class;
    }
}
