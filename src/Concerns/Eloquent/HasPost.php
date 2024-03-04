<?php

namespace Weblebby\Framework\Concerns\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Weblebby\Framework\Facades\NavigationLinkable;
use Weblebby\Framework\Facades\SmartMenu;
use Weblebby\Framework\Items\Field\FieldItem;
use Weblebby\Framework\Items\FieldSectionsItem;
use Weblebby\Framework\Items\NavigationLinkableItem;
use Weblebby\Framework\Items\SmartMenuItem;
use Weblebby\Framework\Items\TaxonomyItem;

trait HasPost
{
    use HasTaxonomies;

    public function scopeSearch(Builder $builder, array $filters = []): Builder
    {
        if (filled($filters['term'] ?? null)) {
            $builder->whereTranslation('title', 'like', "%{$filters['term']}%");
        }

        if (filled($filters['status'] ?? null)) {
            $builder->where('status', $filters['status']);
        }

        return $builder;
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => route('content', $this->slug));
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
                        'prefix' => route('content', '').'/',
                    ])
                    ->rules(['nullable', 'string', 'max:191']),

                FieldItem::text('seo_title')
                    ->translatable()
                    ->label(__('Meta title'))
                    ->hint(__('You can modify the page title that will be displayed on search engines.'))
                    ->rules(['nullable', 'string', 'max:191']),

                FieldItem::textarea('seo_description')
                    ->translatable()
                    ->label(__('Meta description'))
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
        $pluralName = static::getPluralName();

        panel()
            ->permission(static::getModelName())
            ->withTitle($singularName)
            ->withPermissions([
                'create' => __('Can create :name', ['name' => $singularName]),
                'read' => __('Can view :name', ['name' => $pluralName]),
                'update' => __('Can edit :name', ['name' => $pluralName]),
                'delete' => __('Can delete :name', ['name' => $pluralName]),
            ]);

        foreach (static::getTaxonomies() as $taxonomy) {
            $singularName = $taxonomy->singularName();
            $pluralName = $taxonomy->pluralName();

            panel()
                ->permission($taxonomy->name())
                ->withTitle($singularName)
                ->withPermissions([
                    'create' => __('Can create :name', ['name' => $singularName]),
                    'read' => __('Can view :name', ['name' => $pluralName]),
                    'update' => __('Can edit :name', ['name' => $pluralName]),
                    'delete' => __('Can delete :name', ['name' => $pluralName]),
                ]);
        }
    }

    public static function getTaxonomyFor(string $taxonomy): ?TaxonomyItem
    {
        if (! str_starts_with($taxonomy, static::getModelName().'_')) {
            $taxonomy = sprintf('%s_%s', static::getModelName(), $taxonomy);
        }

        return array_values(array_filter(
            static::getTaxonomies(),
            fn (TaxonomyItem $taxonomyItem): bool => $taxonomyItem->name() === $taxonomy
        ))[0] ?? null;
    }

    public static function getNavigationLinkable(): NavigationLinkableItem
    {
        return NavigationLinkableItem::make()
            ->setName(static::getModelName())
            ->setTitle(static::getPluralName())
            ->setModel(static::class)
            // TODO: Add select() to title from translation
            ->setLinks(static::query()->select('id')->take(15)->get());
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
