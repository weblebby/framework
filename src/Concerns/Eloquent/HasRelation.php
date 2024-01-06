<?php

namespace Feadmin\Concerns\Eloquent;

trait HasRelation
{
    public static array $outsideRelations = [];

    public static function setOutsideRelation(string $name, \Closure $callback): void
    {
        static::$outsideRelations[$name] = $callback;
    }

    public function isRelation($key): bool
    {
        return parent::isRelation($key) || isset(static::$outsideRelations[$key]);
    }

    public function __call($method, $parameters)
    {
        if (isset(static::$outsideRelations[$method])) {
            return call_user_func(static::$outsideRelations[$method], $this);
        }

        return parent::__call($method, $parameters);
    }
}
