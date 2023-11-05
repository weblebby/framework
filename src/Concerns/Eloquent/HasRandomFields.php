<?php

namespace Feadmin\Concerns\Eloquent;

use Illuminate\Database\Eloquent\Model;

trait HasRandomFields
{
    public static function bootHasRandomFields(): void
    {
        static::creating(function (Model $model) {
            $model->setRandomFields();
        });
    }

    public function randomFields(): array
    {
        return [];
    }

    public function setRandomFields(): void
    {
        $fields = $this->randomFields();

        foreach ($fields as $field => $callback) {
            if (is_null($this->{$field})) {
                $this->{$field} = $this->makeUnique($field, $callback);
            }
        }
    }

    public function makeUnique($field, $callback): string
    {
        $value = $callback();

        while ($this->where($field, $value)->exists()) {
            $value = $callback();
        }

        return $value;
    }
}
