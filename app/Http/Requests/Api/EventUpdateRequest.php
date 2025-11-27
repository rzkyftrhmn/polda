<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string'],
            'location' => ['nullable','string'],
            'description' => ['nullable','string'],
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date','after_or_equal:start_at'],
            'participants' => ['nullable','array'],
            'participants.*.division_id' => ['required','integer','exists:divisions,id'],
            'participants.*.is_required' => ['nullable','boolean'],
            'participants.*.note' => ['nullable','string'],
        ];
    }
}

