<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Facilities;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceTicketRequest extends FormRequest
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
            'room_id' => 'required|exists:sch_fac_rooms,id',
            'asset_id' => 'nullable|exists:sch_fac_assets,id',
            'reporter_staff_id' => 'required|exists:sch_hr_staff,id',
            'issue_description' => 'required|string|max:2000',
            'priority' => 'required|in:rendah,sedang,tinggi,darurat',
            'cost' => 'nullable|numeric|min:0',
        ];
    }
}
