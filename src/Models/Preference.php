<?php

namespace Feadmin\Models;

use Feadmin\Concerns\Eloquent\HasMetafields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
