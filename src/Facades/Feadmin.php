<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

class Feadmin extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Feadmin::class;
    }
}
