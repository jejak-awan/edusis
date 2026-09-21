<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:sch_std_students,id',
            'category_id' => 'required|exists:sch_std_point_categories,id',
            'points' => 'nullable|integer',
            'description' => 'required|string',
            'date' => 'required|date',
            'admin_notes' => 'nullable|string',

            // Specific fields for violation
            'sanction' => 'nullable|string',
            'sp_level' => 'nullable|in:none,sp1,sp2,sp3',
            'sp_letter_number' => 'nullable|string|max:50',

            // Specific fields for achievement
            'achievement_title' => 'nullable|string|max:150',
            'achievement_level' => 'nullable|in:sekolah,kecamatan,kabupaten,provinsi,nasional,internasional',
            'achievement_rank' => 'nullable|string|max:50',
            'achievement_type' => 'nullable|in:akademik,non_akademik',
        ];
    }
}
