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
            if ($model->isLogging(HasOwnerEnum::CREATED_BY) && auth()->check() && is_null($model->created_by_id)) {
                $model->createdBy()->associate(auth()->user());
            }

            if ($model->isLogging(HasOwnerEnum::UPDATED_BY) && auth()->check() && is_null($model->updated_by_id)) {
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
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }

    public function isLogging(HasOwnerEnum $logType): bool
    {
        if (!property_exists($this, 'userTouches')) {
            return false;
        }

        return in_array($logType, $this->userTouches);
    }
}
