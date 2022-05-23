<?php

namespace Feadmin\Eloquent\Traits;

use Astrotomic\Translatable\Translatable as Base;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait Translatable
{
    use Base;

    public function scopeWithTranslation(Builder $query)
    {
        $query->with([
            'translations' => function (Relation $query) {
                $column = $this->getTranslationsTable() . '.' . $this->getLocaleKey();

                if ($this->useFallback()) {
                    return $query->whereIn($column, $this->getLocalesHelper()->all());
                }

                return $query->where($column, $this->locale());
            },
        ]);
    }
}
