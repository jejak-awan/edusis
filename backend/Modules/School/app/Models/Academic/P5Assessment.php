<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Student;

class P5Assessment extends Model
{
    protected $table = 'sch_acad_p5_assessments';

    protected $fillable = [
        'project_id',
        'student_id',
        'dimension',
        'predicate',
        'notes',
    ];

    /**
     * @return BelongsTo<P5Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(P5Project::class, 'project_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
