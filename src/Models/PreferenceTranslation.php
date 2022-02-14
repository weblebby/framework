<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferenceTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
    ];
}
