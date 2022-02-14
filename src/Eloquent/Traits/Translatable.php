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
                if ($this->useFallback()) {
                    return $query->whereIn(
                        $this->getTranslationsTable() . '.' . $this->getLocaleKey(),
                        $this->getLocalesHelper()->all()
                    );
                }

                return $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), $this->locale());
            },
        ]);
    }
}
