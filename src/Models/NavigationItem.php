<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Core\Enums\NavigationTypeEnum;
use Core\Facades\NavigationLinkableManager;
use Illuminate\Database\Eloquent\Casts\Attribute;

class NavigationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'position',
        'type',
        'linkable_id',
        'linkable_type',
        'link',
        'smart_type',
        'smart_limit',
        'open_in_new_tab',
        'is_active',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'type' => NavigationTypeEnum::class,
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
        return $query
            ->with([
                'children' => function ($query) {
                    $query->oldest('position');
                }
            ]);
    }

    public function url(): Attribute
    {
        return new Attribute(
            get: fn ($value) => match ($this->type) {
                NavigationTypeEnum::LINK => $value,
                NavigationTypeEnum::LINKABLE => $this->linkable->url,
                NavigationTypeEnum::SMART => 'smart',
                NavigationTypeEnum::HOMEPAGE => route('home'),
            }
        );
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
            'is_active',
            'open_in_new_tab',
        ]);

        if ($this->linkable_type) {
            $findLinkable = NavigationLinkableManager::linkables()
                ->firstWhere('model', $this->linkable_type);

            if ($findLinkable) {
                $data['linkable_type'] = $findLinkable['id'];
            }
        }

        return $data;
    }
}
