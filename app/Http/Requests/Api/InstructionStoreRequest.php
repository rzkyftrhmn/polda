<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class InstructionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_uuid' => ['required', 'exists:reports,uuid'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'message' => ['required', 'string'],
        ];
    }
}

