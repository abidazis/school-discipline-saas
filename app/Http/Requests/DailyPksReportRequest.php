<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyPksReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // All authenticated users can view daily reports
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'shift_id' => ['nullable', 'integer', 'min:1'],
            'location_id' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:scheduled,completed,cancelled'],
            'school_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'shift_id.integer' => 'Shift ID harus berupa angka.',
            'location_id.integer' => 'Lokasi ID harus berupa angka.',
            'status.in' => 'Status jadwal tidak valid.',
        ];
    }
}
