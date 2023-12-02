<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Facades\NavigationLinkable;
use Feadmin\Facades\PostModels;
use Feadmin\Facades\SmartMenu;
use Feadmin\Items\NavigationLinkableItem;
use Feadmin\Items\PostSectionsItem;
use Feadmin\Items\SmartMenuItem;
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

    public static function getPostSections(): PostSectionsItem
    {
        return PostSectionsItem::make();
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
        return PostModels::taxonomy(static::getTaxonomyFor($taxonomy))->abilityFor($ability);
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

    public static function getTaxonomyFor(string $taxonomy): string
    {
        return sprintf('%s_%s', static::getModelName(), $taxonomy);
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
}
