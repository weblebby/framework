<?php

namespace Feadmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string name(?string $name)
 * @method static string dottedName(?string $name)
 * @method static string id(?string $id, string $bag = null)
 * @method static bool checked(string $name, mixed $default, $attributes)
 * 
 * @see \Feadmin\Services\FormComponentService
 */
class FormComponent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Feadmin\Services\FormComponentService::class;
    }
}
