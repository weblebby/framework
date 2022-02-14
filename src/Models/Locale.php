<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
