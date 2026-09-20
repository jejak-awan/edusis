<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointCategory extends Model
{
    use HasFactory;

    protected $table = 'sch_std_point_categories';

    protected $fillable = [
        'name',
        'type',
        'default_points',
        'description',
        'is_active',
    ];

    protected $casts = [
        'default_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creditPoints(): HasMany
    {
        return $this->hasMany(CreditPoint::class, 'category_id');
    }
}
