<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $default_points
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CreditPoint> $creditPoints
 */
class PointCategory extends Model
{
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

    /**
     * @return HasMany<CreditPoint, $this>
     */
    public function creditPoints(): HasMany
    {
        return $this->hasMany(CreditPoint::class, 'category_id');
    }
}
