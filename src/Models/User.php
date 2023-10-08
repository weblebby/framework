<?php

namespace Feadmin\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

abstract class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasRoles, Notifiable, InteractsWithMedia;

    abstract public function authorizedPanels(): array|bool;

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    protected function firstName(): Attribute
    {
        return Attribute::get(function ($value) {
            if (in_array('name', $this->fillable)) {
                return explode(' ', $this->name)[0];
            }

            return $value;
        });
    }

    protected function lastName(): Attribute
    {
        return Attribute::get(function ($value) {
            if (in_array('name', $this->fillable)) {
                return last(explode(' ', $this->name));
            }

            return $value;
        });
    }

    protected function shortName(): Attribute
    {
        return Attribute::get(function () {
            $words = explode(' ', $this->name);

            if (count($words) > 1) {
                return collect($words)
                    ->map(fn ($word) => Str::upper(mb_substr($word, 0, 1)))
                    ->implode('');
            }

            return Str::upper(mb_substr($this->name, 0, 2));
        });
    }
}
