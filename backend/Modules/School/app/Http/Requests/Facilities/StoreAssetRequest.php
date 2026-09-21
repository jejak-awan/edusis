<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Facilities;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
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
            'room_id' => 'nullable|exists:sch_fac_rooms,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'category' => 'required|in:elektronik,mebeler,alat_praktik,kendaraan,buku,lainnya',
            'brand_model' => 'nullable|string|max:100',
            'acquisition_year' => 'nullable|integer|min:1990|max:2099',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'source_of_fund' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
