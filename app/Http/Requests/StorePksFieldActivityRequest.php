<?php

namespace App\Http\Requests;

use App\Models\PksDutyAssignment;
use App\Models\PksDutySchedule;
use App\Models\PksFieldActivity;
use App\Models\Violation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePksFieldActivityRequest extends FormRequest
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
            'activity_date' => ['required', 'date'],
            'started_at' => ['required', 'date_format:H:i'],
            'ended_at' => ['nullable', 'date_format:H:i', 'after_or_equal:started_at'],
            'activity_type' => [
                'required',
                Rule::in(array_keys(PksFieldActivity::TYPE_LABELS)),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'finding' => ['nullable', 'string', 'max:2000'],
            'action_taken' => ['nullable', 'string', 'max:2000'],
            'status' => [
                'sometimes',
                Rule::in([
                    PksFieldActivity::STATUS_DRAFT,
                    PksFieldActivity::STATUS_COMPLETED,
                ]),
            ],
            'violation_id' => [
                'nullable',
                'integer',
                Rule::exists('violations', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
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
            $this->validateAssignmentEligibility($validator);
            $this->validateViolationEligibility($validator);
        });
    }

    /**
     * Validate that the assignment is eligible for field activity.
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
            $validator->errors()->add('pks_duty_assignment_id', 'Penugasan tidak aktif dan tidak dapat menerima aktivitas lapangan.');
            return;
        }

        // Schedule must not be cancelled
        if ($assignment->schedule && $assignment->schedule->isCancelled()) {
            $validator->errors()->add('pks_duty_assignment_id', 'Jadwal telah dibatalkan.');
        }
    }

    /**
     * Validate that the violation belongs to the same tenant if provided.
     */
    protected function validateViolationEligibility($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $violationId = $this->input('violation_id');
        $schoolId = $this->getSchoolId();

        if (!$violationId) {
            return;
        }

        $violation = Violation::find($violationId);

        if (!$violation) {
            return;
        }

        if ($violation->school_id !== $schoolId) {
            $validator->errors()->add('violation_id', 'Pelanggaran tidak valid untuk sekolah ini.');
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
            'activity_date.required' => 'Tanggal aktivitas harus diisi.',
            'activity_date.date' => 'Format tanggal tidak valid.',
            'started_at.required' => 'Jam mulai harus diisi.',
            'started_at.date_format' => 'Format jam mulai tidak valid (gunakan format HH:MM).',
            'ended_at.date_format' => 'Format jam selesai tidak valid (gunakan format HH:MM).',
            'ended_at.after_or_equal' => 'Jam selesai tidak boleh sebelum jam mulai.',
            'activity_type.required' => 'Jenis aktivitas harus dipilih.',
            'activity_type.in' => 'Jenis aktivitas tidak valid.',
            'violation_id.exists' => 'Pelanggaran tidak valid.',
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
