<?php

namespace Feadmin\Concerns\Eloquent;

trait HasPosition
{
    protected static function bootHasPosition(): void
    {
        static::creating(function ($model) {
            if (is_null($model->position)) {
                if (is_null($position = $model->getMaxPosition())) {
                    return;
                }

                $model->position = $position + 10;
            }
        });
    }

    public function getMaxPosition(): ?int
    {
        return null;
    }
}
