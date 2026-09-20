<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\System\Models\User;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Semester;

class CreditPoint extends Model
{
    use HasFactory;

    protected $table = 'sch_std_credit_points';

    protected $fillable = [
        'student_id',
        'category_id',
        'academic_year_id',
        'semester_id',
        'points',
        'description',
        'date',
        'reporter_id',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'points' => 'integer',
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PointCategory::class, 'category_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function violation(): HasOne
    {
        return $this->hasOne(Violation::class, 'credit_point_id');
    }

    public function achievement(): HasOne
    {
        return $this->hasOne(Achievement::class, 'credit_point_id');
    }
}
