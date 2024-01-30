<?php

namespace Weblebby\Framework\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Weblebby\Framework\Concerns\Eloquent\HasMetafields;

class Preference extends Model
{
    use HasFactory, HasMetafields;

    protected $fillable = [
        'namespace',
        'bag',
    ];

    public function getNamespaceAndBag(): string
    {
        return "{$this->namespace}::{$this->bag}";
    }
}
