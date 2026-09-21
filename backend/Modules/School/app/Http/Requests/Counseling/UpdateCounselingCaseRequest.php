<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Counseling;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCounselingCaseRequest extends FormRequest
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
            'counselor_id' => 'sometimes|exists:sch_hr_staff,id',
            'category' => 'sometimes|in:pribadi,sosial,belajar,karir',
            'confidentiality' => 'sometimes|in:biasa,internal,rahasia,sangat_rahasia',
            'problem_description' => 'sometimes|string',
            'counseling_steps' => 'nullable|string',
            'solution_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'status' => 'sometimes|in:open,in_progress,resolved,referred',
        ];
    }
}
