<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Metafield extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public function metafieldable(): MorphTo
    {
        return $this->morphTo();
    }
}
