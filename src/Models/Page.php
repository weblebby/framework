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
        $section = FieldSectionsItem::make();

        foreach (parent::getPostSections()->toArray() as $name => $item) {
            $section->add($name, $item['title'], $item['fields']);
        }

        $section->add('product', __('Ürün Sekmeleri'), [
            FieldItem::repeated('metafields.tabs')
                ->label(__('Sekmeler'))
                ->hint(__('Sekme ekleyin.'))
                ->fields([
                    FieldItem::text('title')
                        ->label(__('Sekme Başlığı'))
                        ->rules(['required', 'string']),

                    FieldItem::richText('text')
                        ->label(__('Sekme İçeriği'))
                        ->rules(['nullable', 'string']),
                ]),
        ]);

        $section->add('content', __('Galeri'), [
            FieldItem::repeated('metafields.gallery')
                ->label(__('Galeri'))
                ->hint(__('Fotoğraf ekleyin.'))
                ->fields([
                    FieldItem::image('image')
                        ->label(__('Fotoğraf'))
                        ->rules(['nullable', 'image']),
                ]),
        ]);

        return $section;
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
