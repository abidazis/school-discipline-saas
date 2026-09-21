<?php

namespace App\Http\Requests;

use App\Models\PksDutyAssignment;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePksDutyAssignmentRequest extends FormRequest
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
            'pks_duty_schedule_id' => [
                'required',
                'integer',
                Rule::exists('pks_duty_schedules', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'pks_member_id' => [
                'required',
                'integer',
                Rule::exists('pks_members', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                          ->where('status', PksMember::STATUS_ACTIVE);
                }),
            ],
            'pks_duty_location_id' => [
                'required',
                'integer',
                Rule::exists('pks_duty_locations', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'status' => [
                'sometimes',
                Rule::in([
                    PksDutyAssignment::STATUS_ASSIGNED,
                    PksDutyAssignment::STATUS_REPLACED,
                    PksDutyAssignment::STATUS_CANCELLED,
                ]),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateLocationBelongsToSchedule($validator);
            $this->validateScheduleStatus($validator);
            $this->validateMemberNotAlreadyAssigned($validator);
        });
    }

    /**
     * Validate that the location belongs to the selected schedule.
     */
    protected function validateLocationBelongsToSchedule($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $scheduleId = $this->input('pks_duty_schedule_id');
        $locationId = $this->input('pks_duty_location_id');
        $schoolId = $this->getSchoolId();

        if (!$scheduleId || !$locationId) {
            return;
        }

        // Verify location belongs to this schedule (within same school)
        $schedule = PksDutySchedule::find($scheduleId);

        if (!$schedule) {
            return;
        }

        // Check if schedule belongs to the same school
        if ($schedule->school_id !== $schoolId) {
            $validator->errors()->add('pks_duty_schedule_id', 'Jadwal tidak valid untuk sekolah ini.');
            return;
        }

        // Check if location belongs to this schedule
        $locationBelongsToSchedule = $schedule->locations()
            ->where('pks_duty_locations.id', $locationId)
            ->exists();

        if (!$locationBelongsToSchedule) {
            $validator->errors()->add('pks_duty_location_id', 'Lokasi tidak termasuk dalam jadwal yang dipilih.');
        }
    }

    /**
     * Validate that the schedule allows assignment.
     */
    protected function validateScheduleStatus($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $scheduleId = $this->input('pks_duty_schedule_id');

        if (!$scheduleId) {
            return;
        }

        $schedule = PksDutySchedule::find($scheduleId);

        if (!$schedule) {
            return;
        }

        // Do not allow new assignments to cancelled or completed schedules
        if ($schedule->isCancelled() || $schedule->isCompleted()) {
            $validator->errors()->add(
                'pks_duty_schedule_id',
                'Tidak dapat menambahkan penugasan pada jadwal yang telah dibatalkan atau selesai.'
            );
        }
    }

    /**
     * Validate that the member is not already assigned to another location in this schedule.
     */
    protected function validateMemberNotAlreadyAssigned($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $scheduleId = $this->input('pks_duty_schedule_id');
        $memberId = $this->input('pks_member_id');
        $schoolId = $this->getSchoolId();

        if (!$scheduleId || !$memberId) {
            return;
        }

        // Check if member already has an active assignment for this schedule (any location)
        $existingAssignment = PksDutyAssignment::where('school_id', $schoolId)
            ->where('pks_duty_schedule_id', $scheduleId)
            ->where('pks_member_id', $memberId)
            ->where('status', PksDutyAssignment::STATUS_ASSIGNED)
            ->first();

        if ($existingAssignment) {
            $validator->errors()->add(
                'pks_member_id',
                'Anggota ini sudah ditugaskan pada jadwal yang sama.'
            );
        }
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'pks_duty_schedule_id.required' => 'Jadwal piket harus dipilih.',
            'pks_duty_schedule_id.exists' => 'Jadwal piket tidak valid.',
            'pks_member_id.required' => 'Anggota PKS harus dipilih.',
            'pks_member_id.exists' => 'Anggota PKS tidak valid atau tidak aktif.',
            'pks_duty_location_id.required' => 'Lokasi piket harus dipilih.',
            'pks_duty_location_id.exists' => 'Lokasi piket tidak valid.',
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
