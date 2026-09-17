<?php

namespace App\Http\Requests;

use App\Models\Violation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreViolationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Super Admin, School Admin, Operator, and Teacher can create violations
        return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isOperator() || $user->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolId = $this->getSchoolId();
        $validStatuses = [
            Violation::STATUS_RECORDED,
            Violation::STATUS_VERIFIED,
            Violation::STATUS_CANCELLED,
        ];

        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'violation_type_id' => [
                'required',
                'integer',
                Rule::exists('violation_types', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                          ->where('is_active', true);
                }),
            ],
            'occurred_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'evidences' => ['nullable', 'array', 'max:5'],
            'evidences.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB max per file
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa harus dipilih.',
            'student_id.exists' => 'Siswa tidak valid atau tidak ditemukan.',
            'violation_type_id.required' => 'Jenis pelanggaran harus dipilih.',
            'violation_type_id.exists' => 'Jenis pelanggaran tidak valid atau tidak aktif.',
            'occurred_at.required' => 'Tanggal kejadian harus diisi.',
            'evidences.*.mimes' => 'File bukti harus berformat JPG, PNG, atau WebP.',
            'evidences.*.max' => 'File bukti maksimal 5MB.',
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
