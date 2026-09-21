<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeachingJournalRequest extends FormRequest
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
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'subject_id' => 'required|exists:sch_acad_subjects,id',
            'teacher_id' => 'required|exists:sch_hr_staff,id',
            'schedule_id' => 'nullable|exists:sch_acad_schedules,id',
            'date' => 'required|date',
            'teaching_hour' => 'required|string|max:30',
            'topic' => 'required|string|max:200',
            'competency_description' => 'nullable|string',
            'learning_activity' => 'nullable|string',
            'present_count' => 'integer|min:0',
            'sick_count' => 'integer|min:0',
            'permit_count' => 'integer|min:0',
            'absent_count' => 'integer|min:0',
            'notes_reflection' => 'nullable|string',
            'evidence_photo_path' => 'nullable|string',
        ];
    }
}
