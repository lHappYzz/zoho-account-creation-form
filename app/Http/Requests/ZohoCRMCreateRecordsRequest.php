<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZohoCRMCreateRecordsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'accountName' => ['required', 'string', 'max:200'],
            'accountWebsite' => ['required', 'URL', 'max:255'],
            'accountPhone' => ['required', 'regex:/^\+380\d{9}$/'],

            'dealName' => ['required', 'string', 'max:120'],
            'dealStage' => ['required', 'string', 'max:120'],
        ];
    }
}
