<?php

namespace App\Http\Requests;

use App\Models\PksDutyLocation;
use App\Models\PksDutySchedule;
use App\Models\PksShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePksDutyScheduleRequest extends FormRequest
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
        $schoolId = $this->getSchoolId();

        return [
            'pks_shift_id' => [
                'required',
                'integer',
                Rule::exists('pks_shifts', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                          ->where('status', PksShift::STATUS_ACTIVE);
                }),
            ],
            'schedule_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                Rule::unique('pks_duty_schedules')
                    ->where(function ($query) use ($schoolId) {
                        $query->where('school_id', $schoolId);
                    })
                    ->where(function ($query) {
                        if ($this->filled('pks_shift_id')) {
                            $query->where('pks_shift_id', $this->pks_shift_id);
                        }
                    }),
            ],
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
            'pks_shift_id.exists' => 'Shift tidak valid atau tidak aktif.',
            'schedule_date.required' => 'Tanggal jadwal harus diisi.',
            'schedule_date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'schedule_date.unique' => 'Jadwal dengan tanggal dan shift yang sama sudah ada.',
            'location_ids.required' => 'Minimal satu lokasi harus dipilih.',
            'location_ids.*.exists' => 'Lokasi tidak valid.',
        ];
    }

    /**
     * Get the school ID for validation.
     */
    protected function getSchoolId(): int
    {
        return $this->user()->school_id ?? 0;
    }
}
