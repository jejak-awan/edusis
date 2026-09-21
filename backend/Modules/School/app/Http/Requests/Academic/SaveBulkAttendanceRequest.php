<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class SaveBulkAttendanceRequest extends FormRequest
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
            'date' => 'required|date',
            'records' => 'required|array|min:1',
            'records.*.student_id' => 'required|exists:sch_std_students,id',
            'records.*.status' => 'required|in:H,I,S,A',
            'records.*.notes' => 'nullable|string',
        ];
    }
}
