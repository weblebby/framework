<?php

namespace Feadmin\Models;

use Feadmin\Items\Field\Enums\CodeEditorLanguageEnum;
use Feadmin\Items\Field\FieldItem;
use Feadmin\Items\FieldSectionsItem;
use Feadmin\Items\TaxonomyItem;

class Product extends Post
{
    public static function getSingularName(): string
    {
        return __('Ürün');
    }

    public static function getPluralName(): string
    {
        return __('Ürünler');
    }

    public static function getPostSections(): FieldSectionsItem
    {
        $section = FieldSectionsItem::make();

        foreach (parent::getPostSections()->toArray() as $name => $item) {
            $section->add($name, $item['title'], $item['fields']);
        }

        $section->add('product', __('Ürün Sekmeleri'), [
            FieldItem::repeated('tabs')
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

        $section->add('emin', __('Eminler'), [
            FieldItem::repeated('eminler')
                ->label(__('Eminler'))
                ->hint(__('Emin ekleyin.'))
                ->fields([
                    FieldItem::codeEditor('description')
                        ->editorLanguage(CodeEditorLanguageEnum::HTML)
                        ->label('Açıklama')
                        ->rules(['required', 'string', 'max:5']),

                    FieldItem::image('image')
                        ->label(__('Eminin Fotoğrafı'))
                        ->rules(['required', 'image']),
                ]),
        ]);

        return $section;
    }

    public static function getTaxonomies(): array
    {
        return [
            TaxonomyItem::make('product_category')
                ->withSingularName(__('Ürün kategorisi'))
                ->withPluralName(__('Ürün kategorileri')),

            TaxonomyItem::make('product_tag')
                ->withSingularName(__('Ürün etiketi'))
                ->withPluralName(__('Ürün etiketleri')),
        ];
    }
}
