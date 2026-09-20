<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\HR;

use Illuminate\Foundation\Http\FormRequest;

class StaffCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => 'required|exists:sch_hr_staff,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
