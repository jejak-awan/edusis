<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\StudentAffairs;

use Illuminate\Foundation\Http\FormRequest;

class StoreVotingEventRequest extends FormRequest
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
            'title' => 'required|string|max:150',
            'type' => 'required|in:osis,mpk,ekskul,guru_favorit,polling,komite',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|in:all_students,specific_classes,teachers_only,parents,all',
            'voting_mode' => 'nullable|in:token_kiosk,online_login,hybrid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:draft,active,closed',
            'allow_abstain' => 'nullable|boolean',
        ];
    }
}
