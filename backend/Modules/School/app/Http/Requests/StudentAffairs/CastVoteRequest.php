<?php

declare(strict_types=1);

namespace Modules\School\Http\Requests\StudentAffairs;

use Illuminate\Foundation\Http\FormRequest;

class CastVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'candidate_id' => 'nullable|exists:sch_stda_vote_candidates,id',
            'is_abstain' => 'nullable|boolean',
        ];
    }
}
