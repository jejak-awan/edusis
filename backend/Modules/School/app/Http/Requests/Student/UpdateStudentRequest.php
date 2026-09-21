<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\School\Rules\ValidNIK;
use Modules\School\Rules\ValidNISN;
use Modules\School\Rules\ValidPhoneNumber;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('id') ?? $this->route('student');

        return [
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:L,P',
            'nisn' => [
                'nullable',
                'string',
                'size:10',
                Rule::unique('sch_std_students', 'nisn')->ignore($studentId),
                new ValidNISN,
            ],
            'nis' => 'nullable|string|max:20',
            'nik' => ['nullable', 'string', 'size:16', new ValidNIK],
            'school_level_id' => 'nullable|exists:sch_ins_levels,id',
            'department_id' => 'nullable|exists:sch_acad_departments,id',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => ['nullable', 'string', 'max:25', new ValidPhoneNumber],
            'email' => 'nullable|email|max:100',
            'entry_year' => 'nullable|string|max:10',
            'status' => 'in:active,graduated,transferred,dropped_out',
            'notes' => 'nullable|string',
        ];
    }
}
