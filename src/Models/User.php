<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }
}
