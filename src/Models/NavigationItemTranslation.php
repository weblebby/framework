<?php

namespace Weblebby\Framework\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItemTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];
}
