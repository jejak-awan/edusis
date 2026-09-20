<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Administration;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Rules\ValidPhoneNumber;

class StorePpdbRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:sch_acad_departments,id',
            'second_choice_department_id' => 'nullable|exists:sch_acad_departments,id',
            'track' => 'required|in:zonasi,afirmasi,prestasi_akademik,prestasi_non_akademik,perpindahan_ortu',
            'full_name' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'phone' => ['nullable', 'string', 'max:30', new ValidPhoneNumber],
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:150',
            'parent_phone' => ['nullable', 'string', 'max:30', new ValidPhoneNumber],
            'parent_job' => 'nullable|string|max:100',
            'parent_income' => 'nullable|string|max:50',
            'previous_school' => 'nullable|string|max:150',
            'average_report_score' => 'nullable|numeric|between:0,100',
            'document_paths' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }
}
