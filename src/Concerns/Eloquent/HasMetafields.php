<?php

namespace Feadmin\Concerns\Eloquent;

use Feadmin\Models\Metafield;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMetafields
{
    public function metafields(): MorphMany
    {
        return $this->morphMany(Metafield::class, 'metafieldable');
    }

    public function scopeByMetafield(Builder $builder, string $key, mixed $value): Builder
    {
        return $builder->whereHas('metafields', fn ($q) => $q->where('key', $key)->where('value', $value));
    }

    public function getMetafield(string $key): ?Metafield
    {
        /** @var ?Metafield $metafield */
        $metafield = $this->metafields()->where('key', $key)->first();

        return $metafield;
    }

    public function getMetafieldValue(string $key, mixed $default = null): mixed
    {
        return $this->getMetafield($key)?->value ?? $default;
    }

    public function setMetafield(string $key, mixed $value): ?Metafield
    {
        $metafield = $this->getMetafield($key);

        if ($metafield) {
            if (is_null($value)) {
                $metafield->delete();

                return null;
            }

            $metafield->value = $value;
            $metafield->save();

            return $metafield;
        }

        if (is_null($value)) {
            return null;
        }

        /** @var Metafield $metafield */
        $metafield = $this->metafields()->create([
            'key' => $key,
            'value' => $value,
        ]);

        return $metafield;
    }

    public function deleteMetafield(string $key): bool
    {
        return $this->metafields()->where('key', $key)->delete();
    }
}
