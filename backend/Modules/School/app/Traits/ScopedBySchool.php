<?php

declare(strict_types=1);

namespace Modules\School\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Context;

/**
 * Trait untuk mengisolasi model Eloquent secara otomatis berdasarkan konteks tenant (school_id).
 *
 * @property int $school_id
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ScopedBySchool
{
    /**
     * Boot global scope dan event creating untuk pengisian otomatis school_id.
     */
    public static function bootScopedBySchool(): void
    {
        static::addGlobalScope('school', function (Builder $builder): void {
            $schoolId = Context::get('school_id');
            if ($schoolId) {
                $builder->where($builder->getModel()->getTable() . '.school_id', (int) $schoolId);
            }
        });

        static::creating(function (Model $model): void {
            $schoolId = Context::get('school_id');
            if (is_numeric($schoolId) && ! isset($model->school_id)) {
                $model->school_id = (int) $schoolId;
            }
        });
    }
}
