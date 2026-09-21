<?php

namespace App\Http\Requests;

use App\Models\PksFieldActivity;
use App\Models\Violation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksFieldActivityRequest extends FormRequest
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
        $activity = $this->route('pksFieldActivity');

        return [
            'activity_date' => ['sometimes', 'date'],
            'started_at' => ['sometimes', 'date_format:H:i'],
            'ended_at' => ['nullable', 'date_format:H:i', 'after_or_equal:started_at'],
            'activity_type' => [
                'sometimes',
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
                    PksFieldActivity::STATUS_CANCELLED,
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
            $this->validateViolationEligibility($validator);
            $this->validateCompletedStatusRequirements($validator);
        });
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
     * Validate requirements for completed status.
     */
    protected function validateCompletedStatusRequirements($validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $status = $this->input('status');

        if ($status === PksFieldActivity::STATUS_COMPLETED) {
            // Completed activities should have at least started_at
            $startedAt = $this->input('started_at');
            if (!$startedAt) {
                $activity = $this->route('pks_field_activity');
                if (!$activity || !$activity->started_at) {
                    $validator->errors()->add('started_at', 'Jam mulai harus diisi untuk aktivitas yang selesai.');
                }
            }
        }
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'started_at.date_format' => 'Format jam mulai tidak valid (gunakan format HH:MM).',
            'ended_at.date_format' => 'Format jam selesai tidak valid (gunakan format HH:MM).',
            'ended_at.after_or_equal' => 'Jam selesai tidak boleh sebelum jam mulai.',
            'activity_type.in' => 'Jenis aktivitas tidak valid.',
            'status.in' => 'Status tidak valid.',
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
