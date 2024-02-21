<?php

namespace Weblebby\Framework\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItemTranslation extends Model
{
    use Cachable, HasFactory;

    protected $fillable = [
        'title',
    ];
}
