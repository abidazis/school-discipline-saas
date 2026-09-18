<?php

namespace App\Http\Requests;

use App\Models\PksShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin() || $this->user()->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pksShift = $this->route('pks_shift');

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pks_shifts')->where(function ($query) use ($pksShift) {
                    $query->where('school_id', $pksShift->school_id);
                })->ignore($pksShift),
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['sometimes', Rule::in([PksShift::STATUS_ACTIVE, PksShift::STATUS_INACTIVE])],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Nama shift sudah digunakan di sekolah ini.',
            'start_time.date_format' => 'Format jam mulai harus HH:MM (contoh: 07:00).',
            'end_time.date_format' => 'Format jam selesai harus HH:MM (contoh: 10:00).',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}
