<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Facades\NavigationLinkable;
use Feadmin\Facades\SmartMenu;
use Feadmin\Items\Field\FieldItem;
use Feadmin\Items\NavigationLinkableItem;
use Feadmin\Items\FieldSectionsItem;
use Feadmin\Items\SmartMenuItem;
use Feadmin\Items\TaxonomyItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPost
{
    use HasTaxonomies;

    public function scopeSearch(Builder $builder, array $filters = []): Builder
    {
        if (filled($filters['term'] ?? null)) {
            $builder->where('title', 'like', "%{$filters['term']}%");
        }

        if (filled($filters['status'] ?? null)) {
            $builder->where('status', $filters['status']);
        }

        return $builder;
    }

    public function url(): Attribute
    {
        return Attribute::get(fn() => route('posts.show', $this->slug));
    }

    public function register(): void
    {
        $this->saveAbilitiesToPanel();

        NavigationLinkable::add($this->getNavigationLinkable());
        SmartMenu::add($this->getSmartMenu());
    }

    public static function getModelName(): string
    {
        return str(static::class)->classBasename()->lower()->toString();
    }

    public static function getSingularName(): string
    {
        return str(static::class)->classBasename()->title()->toString();
    }

    public static function getPluralName(): string
    {
        return str(static::class)->classBasename()->plural()->title()->toString();
    }

    public static function getPostSections(): FieldSectionsItem
    {
        return FieldSectionsItem::make()
            ->add('seo', __('SEO'), [
                FieldItem::text('slug')
                    ->translatable()
                    ->label(__('URL'))
                    ->attributes([
                        'prefix' => route('posts.show', '') . '/',
                    ])
                    ->rules(['nullable', 'string', 'max:191']),

                FieldItem::text('seo_title')
                    ->translatable()
                    ->label(__('Meta başlığı'))
                    ->hint(__('Arama motorlarında görünecek sayfa başlığını buradan değiştirebilirsiniz.'))
                    ->rules(['nullable', 'string', 'max:191']),

                FieldItem::textarea('seo_description')
                    ->translatable()
                    ->label(__('Meta açıklaması'))
                    ->attributes(['rows' => 3])
                    ->rules(['nullable', 'string', 'max:400']),
            ]);
    }

    public static function getPostAbilities(): array
    {
        $prefix = static::getModelName();

        return [
            'create' => sprintf('%s:create', $prefix),
            'read' => sprintf('%s:read', $prefix),
            'update' => sprintf('%s:update', $prefix),
            'delete' => sprintf('%s:delete', $prefix),
        ];
    }

    public static function getPostAbilityFor(string $ability): ?string
    {
        return static::getPostAbilities()[$ability] ?? null;
    }

    public static function getTaxonomyAbilityFor(string $taxonomy, string $ability): ?string
    {
        return static::getTaxonomyFor($taxonomy)?->abilityFor($ability);
    }

    public static function saveAbilitiesToPanel(): void
    {
        $singularName = static::getSingularName();

        panel()
            ->permission(static::getModelName())
            ->withTitle(static::getSingularName())
            ->withPermissions([
                'create' => __(':name oluşturabilir', ['name' => $singularName]),
                'read' => __(':name görüntüleyebilir', ['name' => $singularName]),
                'update' => __(':name düzenleyebilir', ['name' => $singularName]),
                'delete' => __(':name silebilir', ['name' => $singularName]),
            ]);

        foreach (static::getTaxonomies() as $taxonomy) {
            $singularName = $taxonomy->singularName();

            panel()
                ->permission($taxonomy->name())
                ->withTitle($singularName)
                ->withPermissions([
                    'create' => __(':name oluşturabilir', ['name' => $singularName]),
                    'read' => __(':name görüntüleyebilir', ['name' => $singularName]),
                    'update' => __(':name düzenleyebilir', ['name' => $singularName]),
                    'delete' => __(':name silebilir', ['name' => $singularName]),
                ]);
        }
    }

    public static function getTaxonomyFor(string $taxonomy): ?TaxonomyItem
    {
        if (!str_starts_with($taxonomy, static::getModelName() . '_')) {
            $taxonomy = sprintf('%s_%s', static::getModelName(), $taxonomy);
        }

        return array_values(
            array_filter(static::getTaxonomies(), fn(TaxonomyItem $taxonomyItem) => $taxonomyItem->name() === $taxonomy)
        )[0] ?? null;
    }

    public static function getNavigationLinkable(): NavigationLinkableItem
    {
        return NavigationLinkableItem::make()
            ->setName(static::getModelName())
            ->setTitle(static::getPluralName())
            ->setModel(static::class)
            ->setLinks(static::query()->select('id', 'title')->take(15)->get());
    }

    public static function getSmartMenu(): SmartMenuItem
    {
        return SmartMenuItem::make()
            ->setName(static::getModelName())
            ->setTitle(static::getPluralName());
    }

    public static function doesSupportTemplates(): bool
    {
        return true;
    }
}
