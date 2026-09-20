<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Administration;

use Illuminate\Foundation\Http\FormRequest;
use Modules\School\Rules\ValidPhoneNumber;

class CheckInVisitorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visitor_name' => 'required|string|max:150',
            'institution' => 'nullable|string|max:150',
            'phone' => ['nullable', 'string', 'max:30', new ValidPhoneNumber()],
            'identity_type' => 'required|in:KTP,SIM,Passport,Lainnya',
            'identity_number' => 'nullable|string|max:50',
            'purpose' => 'required|string|max:255',
            'target_person' => 'required|string|max:150',
            'badge_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
