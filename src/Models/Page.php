<?php

namespace Weblebby\Framework\Models;

use Weblebby\Framework\Items\FieldSectionsItem;
use Weblebby\Framework\Items\TaxonomyItem;

class Page extends Post
{
    public static function getSingularName(): string
    {
        return __('Page');
    }

    public static function getPluralName(): string
    {
        return __('Pages');
    }

    public static function getPostSections(): FieldSectionsItem
    {
        return parent::getPostSections();
    }

    public static function getTaxonomies(): array
    {
        return [
            TaxonomyItem::make('page_category')
                ->withSingularName(__('Page category'))
                ->withPluralName(__('Page categories')),

            TaxonomyItem::make('page_tag')
                ->withSingularName(__('Page tag'))
                ->withPluralName(__('Page tags')),
        ];
    }
}
