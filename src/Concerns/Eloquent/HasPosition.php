<?php

namespace Feadmin\Concerns\Eloquent;

trait HasPosition
{
    protected static function bootHasPosition(): void
    {
        static::creating(function ($model) {
            if (is_null($model->position)) {
                if (is_null($position = $model->maxPosition())) {
                    return;
                }

                $model->position = $position + 10;
            }
        });
    }

    public function maxPosition(): ?int
    {
        return null;
    }
}
