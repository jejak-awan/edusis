<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Rules\ValidNIK;
use Modules\School\Rules\ValidNISN;
use Modules\School\Rules\ValidPhoneNumber;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Student identity attributes
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:L,P',
            'nisn' => ['nullable', 'string', 'size:10', 'unique:sch_std_students,nisn', new ValidNISN],
            'nis' => 'nullable|string|max:20',
            'nik' => ['nullable', 'string', 'size:16', new ValidNIK],
            'school_level_id' => 'nullable|exists:sch_ins_levels,id',
            'department_id' => 'nullable|exists:sch_acad_departments,id',
            'study_group_id' => 'nullable|exists:sch_acad_study_groups,id',
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

            // Parent / Guardian attributes
            'parent' => 'nullable|array',
            'parent.father_name' => 'nullable|string|max:100',
            'parent.father_nik' => ['nullable', 'string', 'size:16', new ValidNIK],
            'parent.father_occupation' => 'nullable|string|max:100',
            'parent.father_income' => 'nullable|string|max:50',
            'parent.father_phone' => ['nullable', 'string', 'max:25', new ValidPhoneNumber],
            'parent.mother_name' => 'nullable|string|max:100',
            'parent.mother_nik' => ['nullable', 'string', 'size:16', new ValidNIK],
            'parent.mother_occupation' => 'nullable|string|max:100',
            'parent.mother_income' => 'nullable|string|max:50',
            'parent.mother_phone' => ['nullable', 'string', 'max:25', new ValidPhoneNumber],
            'parent.guardian_name' => 'nullable|string|max:100',
            'parent.guardian_nik' => ['nullable', 'string', 'size:16', new ValidNIK],
            'parent.guardian_relation' => 'nullable|string|max:50',
            'parent.guardian_phone' => ['nullable', 'string', 'max:25', new ValidPhoneNumber],
        ];
    }
}
