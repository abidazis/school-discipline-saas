<?php

namespace App\Http\Requests;

use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksDutyScheduleRequest extends FormRequest
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
        $pksDutySchedule = $this->route('pks_duty_schedule');
        $schoolId = $pksDutySchedule->school_id;

        return [
            'pks_shift_id' => [
                'required',
                'integer',
                Rule::exists('pks_shifts', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'schedule_date' => ['required', 'date', 'date_format:Y-m-d'],
            'status' => ['sometimes', Rule::in([
                PksDutySchedule::STATUS_SCHEDULED,
                PksDutySchedule::STATUS_COMPLETED,
                PksDutySchedule::STATUS_CANCELLED,
            ])],
            'location_ids' => ['required', 'array', 'min:1'],
            'location_ids.*' => [
                'integer',
                Rule::exists('pks_duty_locations', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'pks_shift_id.required' => 'Shift harus dipilih.',
            'pks_shift_id.exists' => 'Shift tidak valid.',
            'schedule_date.required' => 'Tanggal jadwal harus diisi.',
            'location_ids.required' => 'Minimal satu lokasi harus dipilih.',
            'location_ids.*.exists' => 'Lokasi tidak valid.',
        ];
    }
}
