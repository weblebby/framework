<?php

namespace Weblebby\Framework\Contracts\Eloquent;

use Weblebby\Framework\Items\FieldSectionsItem;
use Weblebby\Framework\Items\NavigationLinkableItem;
use Weblebby\Framework\Items\SmartMenuItem;
use Weblebby\Framework\Items\TaxonomyItem;

interface PostInterface
{
    public function register(): void;

    public static function getModelName(): string;

    public static function getSingularName(): string;

    public static function getPluralName(): string;

    public static function getPostSections(): FieldSectionsItem;

    public static function getPostAbilities(): array;

    public static function getPostAbilityFor(string $ability): ?string;

    public static function getTaxonomyAbilityFor(string $taxonomy, string $ability): ?string;

    public static function saveAbilitiesToPanel(): void;

    public static function getTaxonomyFor(string $taxonomy): ?TaxonomyItem;

    /**
     * @return array<int, TaxonomyItem>
     */
    public static function getTaxonomies(): array;

    public static function getNavigationLinkable(): NavigationLinkableItem;

    public static function getSmartMenu(): SmartMenuItem;

    public static function doesSupportTemplates(): bool;
}
