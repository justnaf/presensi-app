<?php

namespace App\Http\Requests\Admin\Events;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Assuming authorization is handled by controller middleware
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'about' => 'nullable|string',
            'location_name' => 'nullable|string|max:255',
            'location_url' => 'nullable|url|max:255',
            'speaker' => 'nullable|string|max:255',
            'poster_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_attendees' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:event_categories,id',
            'type' => 'required|string|max:255',
            'attendance_mode' => 'required|in:ticketing,barcode',

            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            // Validation for an array of rundowns
            'rundowns' => 'nullable|array',
            'rundowns.*.title' => 'required_with:rundowns|string|max:255',
            'rundowns.*.start_time' => 'required_with:rundowns|date',
            'rundowns.*.end_time' => 'required_with:rundowns|date|after_or_equal:rundowns.*.start_time',
            'rundowns.*.description' => 'nullable|string',
        ];
    }
}
