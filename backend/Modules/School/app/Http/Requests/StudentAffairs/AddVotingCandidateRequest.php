<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\StudentAffairs;

use Illuminate\Foundation\Http\FormRequest;

class AddVotingCandidateRequest extends FormRequest
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
            'candidate_number' => 'required|integer|min:1',
            'title' => 'required|string|max:150',
            'leader_student_id' => 'nullable|exists:sch_std_students,id',
            'vice_student_id' => 'nullable|exists:sch_std_students,id',
            'staff_id' => 'nullable|exists:sch_hr_staff,id',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo_url' => 'nullable|string|max:255',
        ];
    }
}
