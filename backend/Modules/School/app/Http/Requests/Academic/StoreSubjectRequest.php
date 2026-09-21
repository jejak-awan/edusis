<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Academic;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'code' => 'nullable|string|max:30',
            'name' => 'required|string|max:100',
            'group' => 'required|string|max:30',
            'kkm' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ];
    }
}
