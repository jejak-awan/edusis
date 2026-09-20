<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\StudentAffairs;

use Illuminate\Foundation\Http\FormRequest;

class GenerateVotingVotersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source' => 'required|in:students,all_students,staff,all_school',
            'study_group_id' => 'nullable|exists:sch_acad_study_groups,id',
        ];
    }
}
