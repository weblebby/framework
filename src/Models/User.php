<?php

namespace Weblebby\Framework\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Weblebby\Framework\Concerns\Eloquent\HasRelation;

abstract class User extends Authenticatable implements HasMedia
{
    use HasFactory, HasRelation, HasRoles, InteractsWithMedia, Notifiable;

    abstract public function authorizedPanels(): array|bool;

    public function canAccessPanel(string $panel): bool
    {
        $authorizedPanels = $this->authorizedPanels();

        if (is_bool($authorizedPanels)) {
            return $authorizedPanels;
        }

        if (is_array($authorizedPanels)) {
            return in_array($panel, $authorizedPanels);
        }

        return false;
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
