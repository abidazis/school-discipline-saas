<?php

namespace App\Http\Requests;

use App\Models\PksMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePksMemberRequest extends FormRequest
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

        $validPositions = [
            PksMember::POSITION_MEMBER,
            PksMember::POSITION_KORLAP,
            PksMember::POSITION_WAKIL_KORLAP,
            PksMember::POSITION_KETUA,
            PksMember::POSITION_WAKIL_KETUA,
        ];

        return [
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId)
                          ->where('status', 'active');
                }),
            ],
            'position' => ['required', 'string', 'max:50'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'graduated', 'resigned'])],
            'joined_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:joined_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Siswa harus dipilih.',
            'student_id.exists' => 'Siswa tidak valid atau tidak ditemukan.',
            'position.required' => 'Jabatan harus diisi.',
            'joined_at.required' => 'Tanggal bergabung harus diisi.',
            'ended_at.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal bergabung.',
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
