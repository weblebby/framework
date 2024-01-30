<?php

namespace Weblebby\Framework\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
    public function isDefault(): Attribute
    {
        return Attribute::get(fn () => in_array($this->name, ['Super Admin']));
    }
}
