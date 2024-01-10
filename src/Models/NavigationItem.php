<?php

namespace Feadmin\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Feadmin\Concerns\Eloquent\Translatable;
use Feadmin\Enums\NavigationTypeEnum;
use Feadmin\Services\TaxonomyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NavigationItem extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = [
        'position',
        'type',
        'linkable_id',
        'linkable_type',
        'link',
        'smart_type',
        'smart_limit',
        'smart_filters',
        'smart_sort',
        'smart_view_all',
        'open_in_new_tab',
        'is_active',
    ];

    public $translatedAttributes = [
        'title',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'type' => NavigationTypeEnum::class,
        'smart_filters' => 'array',
        'smart_sort' => 'array',
        'smart_view_all' => 'boolean',
    ];

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function navigation(): BelongsTo
    {
        return $this->belongsTo(Navigation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')->withRecursiveChildren();
    }

    public function scopeWithRecursiveChildren(Builder $query): Builder
    {
        return $query->with(['children' => fn ($query) => $query->oldest('position')]);
    }

    public function url(): Attribute
    {
        return Attribute::get(fn ($value) => match ($this->type) {
            NavigationTypeEnum::LINK => $value,
            NavigationTypeEnum::LINKABLE => $this->linkable->url,
            NavigationTypeEnum::SMART => 'smart',
            NavigationTypeEnum::HOMEPAGE => route('home'),
        });
    }

    public function toExport(): array
    {
        $data = $this->only([
            'id',
            'title',
            'type',
            'linkable_type',
            'linkable_id',
            'link',
            'smart_type',
            'smart_limit',
            'smart_sort',
            'smart_view_all',
            'is_active',
            'open_in_new_tab',
        ]);

        /** @var TaxonomyService $taxonomyService */
        $taxonomyService = app(TaxonomyService::class);

        foreach ($this->smart_filters ?? [] as $key => $taxonomyIds) {
            $data['smart_condition'] = $key;

            foreach ($taxonomyIds as $taxonomyId) {
                $taxonomy = $taxonomyService->getTaxonomyById($taxonomyId);

                if (is_null($taxonomy)) {
                    continue;
                }

                $data['smart_filters'][] = [
                    'value' => $taxonomyId,
                    'label' => $taxonomy->term->title,
                ];
            }
        }

        return $data;
    }
}
