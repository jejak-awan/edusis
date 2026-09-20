<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkGradesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'required|exists:sch_std_students,id',
            'grades.*.score' => 'required|numeric|min:0|max:100',
            'grades.*.remedial_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.notes' => 'nullable|string|max:500',
        ];
    }
}
