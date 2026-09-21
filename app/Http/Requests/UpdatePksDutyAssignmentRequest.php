<?php

namespace App\Http\Requests;

use App\Models\PksDutyAssignment;
use App\Models\PksDutySchedule;
use App\Models\PksMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksDutyAssignmentRequest extends FormRequest
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
        $assignment = $this->route('pksDutyAssignment');

        return [
            'pks_duty_schedule_id' => [
                'sometimes',
                'integer',
                Rule::exists('pks_duty_schedules', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'pks_member_id' => [
                'sometimes',
                'integer',
                Rule::exists('pks_members', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                          ->where('status', PksMember::STATUS_ACTIVE);
                }),
            ],
            'pks_duty_location_id' => [
                'sometimes',
                'integer',
                Rule::exists('pks_duty_locations', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'status' => [
                'required',
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

        $assignment = $this->route('pks_duty_assignment');
        $scheduleId = $this->input('pks_duty_schedule_id', $assignment?->pks_duty_schedule_id);
        $locationId = $this->input('pks_duty_location_id', $assignment?->pks_duty_location_id);
        $schoolId = $this->getSchoolId();

        if (!$scheduleId || !$locationId || !$assignment) {
            return;
        }

        $schedule = PksDutySchedule::find($scheduleId);

        if (!$schedule) {
            return;
        }

        if ($schedule->school_id !== $schoolId) {
            $validator->errors()->add('pks_duty_schedule_id', 'Jadwal tidak valid untuk sekolah ini.');
            return;
        }

        $locationBelongsToSchedule = $schedule->locations()
            ->where('pks_duty_locations.id', $locationId)
            ->exists();

        if (!$locationBelongsToSchedule) {
            $validator->errors()->add('pks_duty_location_id', 'Lokasi tidak termasuk dalam jadwal yang dipilih.');
        }
    }

    /**
     * Validate that the schedule allows assignment updates.
     */
    protected function validateScheduleStatus($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $assignment = $this->route('pks_duty_assignment');
        $scheduleId = $this->input('pks_duty_schedule_id', $assignment?->pks_duty_schedule_id);

        if (!$scheduleId || !$assignment) {
            return;
        }

        $schedule = PksDutySchedule::find($scheduleId);

        if (!$schedule) {
            return;
        }

        // Allow status changes even for completed/cancelled schedules (for history management)
        // But validate for other field changes
        $newStatus = $this->input('status');
        $fieldsChanged = $this->isNotFilled('pks_duty_schedule_id') &&
                        $this->isNotFilled('pks_member_id') &&
                        $this->isNotFilled('pks_duty_location_id');

        if ($fieldsChanged) {
            return;
        }

        if ($schedule->isCancelled()) {
            $validator->errors()->add(
                'pks_duty_schedule_id',
                'Tidak dapat mengubah penugasan pada jadwal yang telah dibatalkan.'
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

        $assignment = $this->route('pks_duty_assignment');
        $scheduleId = $this->input('pks_duty_schedule_id', $assignment?->pks_duty_schedule_id);
        $memberId = $this->input('pks_member_id', $assignment?->pks_member_id);
        $schoolId = $this->getSchoolId();

        if (!$scheduleId || !$memberId || !$assignment) {
            return;
        }

        // If member is not changing, skip validation
        if ($memberId == $assignment->pks_member_id) {
            return;
        }

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
            'pks_duty_schedule_id.exists' => 'Jadwal piket tidak valid.',
            'pks_member_id.exists' => 'Anggota PKS tidak valid atau tidak aktif.',
            'pks_duty_location_id.exists' => 'Lokasi piket tidak valid.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status tidak valid.',
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
