<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Counseling;

use Illuminate\Foundation\Http\FormRequest;

class StoreCounselingCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:sch_std_students,id',
            'counselor_id' => 'required|exists:sch_hr_staff,id',
            'violation_id' => 'nullable|exists:sch_std_violations,id',
            'date' => 'required|date',
            'category' => 'required|in:pribadi,sosial,belajar,karir',
            'confidentiality' => 'required|in:biasa,internal,rahasia,sangat_rahasia',
            'problem_description' => 'required|string|max:2000',
            'counseling_steps' => 'nullable|string',
            'solution_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'status' => 'in:open,in_progress,resolved,referred',
        ];
    }
}
