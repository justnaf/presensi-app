<?php

namespace App\Http\Requests\Admin\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateEventCategoryRequest extends FormRequest
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
        $categoryId = $this->route('event_category')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_categories', 'name')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
            // Tambahkan baris ini
            'slug' => [
                'required',
                'string',
                Rule::unique('event_categories', 'slug')->ignore($categoryId),
            ],
        ];
    }


    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->name) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}
