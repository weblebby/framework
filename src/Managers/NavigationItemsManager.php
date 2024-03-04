<?php

namespace Weblebby\Framework\Managers;

use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Enums\NavigationTypeEnum;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Models\Navigation;
use Weblebby\Framework\Models\NavigationItem;

class NavigationItemsManager
{
    protected array $preloadedItems = [];

    public function get(string $handle): ?array
    {
        if ($this->preloadedItems[$handle] ?? null) {
            return $this->preloadedItems[$handle];
        }

        /** @var Navigation $navigation */
        $navigation = Navigation::query()
            ->select('id')
            ->with([
                'items' => fn ($q) => $q
                    ->select([
                        'id', 'navigation_id', 'parent_id', 'type', 'linkable_type', 'linkable_id',
                        'link', 'smart_type', 'smart_limit', 'smart_filters', 'smart_view_all',
                        'open_in_new_tab',
                    ])
                    ->withTranslation()
                    ->withActiveRecursiveChildren()
                    ->activated()
                    ->whereNull('parent_id')
                    ->oldest('position'),
            ])
            ->where('handle', $handle)
            ->first();

        if (is_null($navigation)) {
            return null;
        }

        return $this->preloadedItems[$handle] = $navigation->items
            ->map(fn (NavigationItem $item) => $this->mapNavigationItem($item))
            ->toArray();
    }

    public function mapNavigationItem(NavigationItem $item): array
    {
        $item->append('url');

        if ($item->type === NavigationTypeEnum::SMART) {
            $postable = PostModels::find($item->smart_type);

            if ($postable) {
                return $this->getItemsFromSmartMenu($postable, $item);
            }
        }

        if ($item->children->isNotEmpty()) {
            $item->children->transform(fn (NavigationItem $item) => $this->mapNavigationItem($item));
        }

        return $item->toArray();
    }

    public function getItemsFromSmartMenu(PostInterface $postable, NavigationItem $item): array
    {
        $query = $postable::query()
            ->withTranslation()
            ->select('id');

        foreach ($item->smart_filters as $ids) {
            $query->hasAnyTaxonomy($ids);
        }

        if ($item->smart_limit) {
            $query->limit(min($item->smart_limit, 20));
        }

        $smartItems = $query->get()
            ->append('url')
            ->map(function (PostInterface $postable) use ($item) {
                $postable->open_in_new_tab = $item->open_in_new_tab;

                return $postable;
            })
            ->toArray();

        // TODO: Find a more effective way to do this.
        if ($item->smart_view_all) {
            if ($postable = PostModels::find($item->smart_type)) {
                if ($primaryTaxonomy = ($postable::getTaxonomies()[0] ?? null)) {
                    $smartItems[] = [
                        'title' => __('View all'),
                        'url' => $primaryTaxonomy->url(),
                        'open_in_new_tab' => $item->open_in_new_tab,
                    ];
                }
            }
        }

        return [
            ...$item->toArray(),
            'url' => '#',
            'children' => $smartItems,
        ];
    }
}
