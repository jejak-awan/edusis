<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\Administration;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:incoming,outgoing',
            'letter_number' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'category' => 'required|in:invitation,assignment,decree,certificate,circular,general',
            'sender' => 'required|string|max:200',
            'recipient' => 'required|string|max:200',
            'subject' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'received_or_sent_date' => 'required|date',
            'status' => 'in:draft,registered,dispatched,archived',
            'disposition_to' => 'nullable|string|max:200',
            'disposition_instruction' => 'nullable|string',
            'disposition_status' => 'in:pending,processed,completed',
            'file_path' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ];
    }
}
