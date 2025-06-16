<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInstitutionRequest extends FormRequest
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
            // Aturan 'unique' di sini mengabaikan ID institusi yang sedang diedit
            'name' => ['required', 'string', 'max:255', Rule::unique('institutions')->ignore($this->institution)],
            'type' => ['nullable', 'string', 'max:255'],
        ];
    }
}
