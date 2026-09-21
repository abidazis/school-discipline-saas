<?php

namespace App\Http\Requests;

use App\Models\PksDutyAssignment;
use App\Models\PksDutyAttendance;
use App\Models\PksDutySchedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePksDutyAttendanceRequest extends FormRequest
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
        $schoolId = $this->getSchoolId();

        return [
            'pks_duty_assignment_id' => [
                'required',
                'integer',
                Rule::exists('pks_duty_assignments', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
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
            $this->validateAssignmentEligibility($validator);
            $this->validateNoExistingAttendance($validator);
            $this->validateCheckInRequirement($validator);
        });
    }

    /**
     * Validate that the assignment is eligible for attendance.
     */
    protected function validateAssignmentEligibility($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $assignmentId = $this->input('pks_duty_assignment_id');
        $schoolId = $this->getSchoolId();

        if (!$assignmentId) {
            return;
        }

        $assignment = PksDutyAssignment::with('schedule')->find($assignmentId);

        if (!$assignment) {
            return;
        }

        // Verify assignment belongs to current school
        if ($assignment->school_id !== $schoolId) {
            $validator->errors()->add('pks_duty_assignment_id', 'Penugasan tidak valid untuk sekolah ini.');
            return;
        }

        // Assignment must be in assigned status
        if (!$assignment->isAssigned()) {
            $validator->errors()->add('pks_duty_assignment_id', 'Penugasan tidak aktif dan tidak dapat menerima kehadiran.');
            return;
        }

        // Schedule must not be cancelled
        if ($assignment->schedule && $assignment->schedule->isCancelled()) {
            $validator->errors()->add('pks_duty_assignment_id', 'Jadwal telah dibatalkan.');
        }
    }

    /**
     * Validate that no attendance exists for this assignment.
     */
    protected function validateNoExistingAttendance($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $assignmentId = $this->input('pks_duty_assignment_id');
        $schoolId = $this->getSchoolId();

        if (!$assignmentId) {
            return;
        }

        $existingAttendance = PksDutyAttendance::where('school_id', $schoolId)
            ->where('pks_duty_assignment_id', $assignmentId)
            ->first();

        if ($existingAttendance) {
            $validator->errors()->add(
                'pks_duty_assignment_id',
                'Kehadiran untuk penugasan ini sudah tercatat.'
            );
        }
    }

    /**
     * Validate check-in requirement based on status.
     */
    protected function validateCheckInRequirement($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $status = $this->input('status');
        $checkIn = $this->input('check_in_at');

        // For present and late, check_in_at is recommended but not strictly required
        // The user can manually set status regardless of actual time
        // This allows flexibility for historical data entry

        if (in_array($status, [PksDutyAttendance::STATUS_ABSENT, PksDutyAttendance::STATUS_EXCUSED])) {
            // Absent and excused don't need check-in
            return;
        }

        // For present and late, check-out must be after check-in if both are provided
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
            'pks_duty_assignment_id.required' => 'Penugasan harus dipilih.',
            'pks_duty_assignment_id.exists' => 'Penugasan tidak valid.',
            'status.required' => 'Status kehadiran harus dipilih.',
            'status.in' => 'Status kehadiran tidak valid.',
            'check_in_at.date' => 'Format jam masuk tidak valid.',
            'check_out_at.date' => 'Format jam pulang tidak valid.',
            'check_out_at.after_or_equal' => 'Jam pulang tidak boleh sebelum jam masuk.',
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
