<?php

namespace Feadmin\Concerns\Eloquent;

use App\Models\User;
use Feadmin\Enums\HasOwnerEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOwner
{
    protected static function bootHasOwner(): void
    {
        self::creating(function (Model $model) {
            if ($model->isLogging(HasOwnerEnum::CREATED_BY) && auth()->check() && is_null($model->getAttribute($model->createdByIdColumn()))) {
                $model->createdBy()->associate(auth()->user());
            }

            if ($model->isLogging(HasOwnerEnum::UPDATED_BY) && auth()->check() && is_null($model->getAttribute($model->deletedByIdColumn()))) {
                $model->updatedBy()->associate(auth()->user());
            }
        });

        self::updating(function (Model $model) {
            if ($model->isLogging(HasOwnerEnum::UPDATED_BY) && auth()->check()) {
                $model->updatedBy()->associate(auth()->user());
            }
        });

        self::deleting(function (Model $model) {
            if ($model->isLogging(HasOwnerEnum::DELETED_BY) && auth()->check()) {
                $model->deletedBy()->associate(auth()->user());
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->createdByIdColumn());
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->updatedByIdColumn());
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->deletedByIdColumn());
    }

    public function createdByIdColumn(): string
    {
        return 'created_by_id';
    }

    public function updatedByIdColumn(): string
    {
        return 'updated_by_id';
    }

    public function deletedByIdColumn(): string
    {
        return 'deleted_by_id';
    }

    public function isLogging(HasOwnerEnum $logType): bool
    {
        if (! property_exists($this, 'userTouches')) {
            return false;
        }

        return in_array($logType, $this->userTouches);
    }
}
