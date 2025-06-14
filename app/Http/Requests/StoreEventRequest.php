<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'about' => 'nullable|string',
            'location_name' => 'nullable|string|max:255',
            'location_url' => 'nullable|url:http,https|max:255',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'speaker' => 'nullable|string|max:255',
            'poster_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'max_attendees' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:event_categories,id',
            'type' => 'required|string|max:255',
            'attendance_mode' => 'required|in:ticketing,barcode',
        ];
    }

    /**
     * Get the custom validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Pesan untuk aturan 'required'
            '*.required' => ':attribute tidak boleh kosong.',

            // Pesan spesifik per field
            'name.required' => 'Nama event wajib diisi.',
            'max_attendees.required' => 'Kapasitas peserta wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'type.required' => 'Tipe event wajib diisi.',
            'attendance_mode.required' => 'Mode kehadiran wajib dipilih.',

            // Pesan untuk aturan lainnya
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute tidak boleh lebih dari :max karakter.',
            'url' => ':attribute harus berupa URL yang valid.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berformat: :values.',
            'poster_image.max' => 'Ukuran :attribute tidak boleh lebih dari 2MB.',
            'min' => ':attribute minimal harus :min.',
            'date' => ':attribute harus dalam format tanggal yang benar.',
            'end_date.after_or_equal' => ':attribute harus sama dengan atau setelah tanggal mulai.',
            'exists' => ':attribute yang dipilih tidak valid atau tidak ada.',
            'in' => ':attribute yang dipilih tidak valid.',
            'between' => ':attribute harus bernilai antara :min dan :max.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama event',
            'description' => 'Deskripsi',
            'about' => 'Tentang event',
            'location_name' => 'Nama lokasi',
            'location_url' => 'URL peta lokasi',
            'longitude' => 'Garis bujur (longitude)',
            'latitude' => 'Garis lintang (latitude)',
            'speaker' => 'Nama pembicara',
            'poster_image' => 'Gambar poster',
            'max_attendees' => 'Kapasitas peserta',
            'start_date' => 'Tanggal mulai',
            'end_date' => 'Tanggal selesai',
            'category_id' => 'Kategori event',
            'type' => 'Tipe event',
            'attendance_mode' => 'Mode kehadiran',
        ];
    }
}
