<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Facilities;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:30',
            'name' => 'required|string|max:100',
            'type' => 'required|in:classroom,laboratory,workshop,library,hall,office,other',
            'capacity' => 'nullable|integer|min:1',
            'floor' => 'nullable|integer|min:1',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ];
    }
}
