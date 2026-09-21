<?php

namespace App\Http\Requests;

use App\Models\PksDutyAttendance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksDutyAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin() ||
               $this->user()->isSchoolAdmin() ||
               $this->user()->isOperator();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $attendance = $this->route('pksDutyAttendance');

        return [
            'status' => [
                'required',
                Rule::in([
                    PksDutyAttendance::STATUS_PRESENT,
                    PksDutyAttendance::STATUS_LATE,
                    PksDutyAttendance::STATUS_ABSENT,
                    PksDutyAttendance::STATUS_EXCUSED,
                ]),
            ],
            'check_in_at' => ['nullable', 'date'],
            'check_out_at' => ['nullable', 'date', 'after_or_equal:check_in_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateCheckOutTiming($validator);
        });
    }

    /**
     * Validate check-out timing.
     */
    protected function validateCheckOutTiming($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $checkInTime = $this->input('check_in_at');
        $checkOutTime = $this->input('check_out_at');

        if ($checkInTime && $checkOutTime) {
            $checkInDateTime = \Carbon\Carbon::parse($checkInTime);
            $checkOutDateTime = \Carbon\Carbon::parse($checkOutTime);

            if ($checkOutDateTime->lessThan($checkInDateTime)) {
                $validator->errors()->add('check_out_at', 'Jam pulang tidak boleh sebelum jam masuk.');
            }
        }
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status kehadiran harus dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
            'check_in_at.date' => 'Format jam masuk tidak valid.',
            'check_out_at.date' => 'Format jam pulang tidak valid.',
            'check_out_at.after_or_equal' => 'Jam pulang tidak boleh sebelum jam masuk.',
        ];
    }
}
