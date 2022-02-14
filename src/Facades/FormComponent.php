<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

class FormComponent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\FormComponentService::class;
    }
}
