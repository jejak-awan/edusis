<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'subject_id' => 'required|exists:sch_acad_subjects,id',
            'teacher_id' => 'required|exists:sch_hr_staff,id',
            'learning_objective_id' => 'nullable|exists:sch_acad_learning_objectives,id',
            'title' => 'required|string|max:150',
            'type' => 'required|in:formative,summative_material,summative_final',
            'weight' => 'integer|min:1|max:10',
            'max_score' => 'integer|min:10|max:100',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ];
    }
}
