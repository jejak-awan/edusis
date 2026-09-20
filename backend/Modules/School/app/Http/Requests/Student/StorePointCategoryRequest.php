<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StorePointCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:pelanggaran,prestasi',
            'default_points' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
