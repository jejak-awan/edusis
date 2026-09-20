<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Rules\ValidNIK;
use Modules\School\Rules\ValidPhoneNumber;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:L,P',
            'nip' => 'nullable|string|max:30|unique:sch_hr_staff,nip',
            'nuptk' => 'nullable|string|max:20|unique:sch_hr_staff,nuptk',
            'nik' => ['nullable', 'string', 'size:16', new ValidNIK()],
            'title_prefix' => 'nullable|string|max:30',
            'title_suffix' => 'nullable|string|max:30',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'employment_status' => 'required|in:PNS,PPPK,GTY,PTY,Honorer,GTT,PTT',
            'role_title' => 'required|string|max:100',
            'last_education' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
            'certification_status' => 'boolean',
            'sk_number' => 'nullable|string|max:100',
            'phone' => ['nullable', 'string', 'max:25', new ValidPhoneNumber()],
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
