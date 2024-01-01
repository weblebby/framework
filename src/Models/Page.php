<?php

namespace Feadmin\Models;

use Feadmin\Items\Field\FieldItem;
use Feadmin\Items\FieldSectionsItem;
use Feadmin\Items\TaxonomyItem;

class Page extends Post
{
    public static function getSingularName(): string
    {
        return __('Sayfa');
    }

    public static function getPluralName(): string
    {
        return __('Sayfalar');
    }

    public static function getPostSections(): FieldSectionsItem
    {
        return parent::getPostSections();
    }

    public static function getTaxonomies(): array
    {
        return [
            TaxonomyItem::make('page_category')
                ->withSingularName(__('Sayfa kategorisi'))
                ->withPluralName(__('Sayfa kategorileri')),

            TaxonomyItem::make('page_tag')
                ->withSingularName(__('Sayfa etiketi'))
                ->withPluralName(__('Sayfa etiketleri')),
        ];
    }
}
