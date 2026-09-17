<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Super Admin, School Admin, and Operator can create students
        return $user->isSuperAdmin() || $user->isSchoolAdmin() || $user->isOperator();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolId = $this->getSchoolId();
        $validGenders = [Student::GENDER_MALE, Student::GENDER_FEMALE];
        $validStatuses = [
            Student::STATUS_ACTIVE,
            Student::STATUS_INACTIVE,
            Student::STATUS_GRADUATED,
            Student::STATUS_TRANSFERRED,
        ];

        return [
            'academic_year_id' => [
                'required',
                'integer',
                Rule::exists('academic_years', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'school_class_id' => [
                'required',
                'integer',
                Rule::exists('school_classes', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'nis' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'nisn' => ['nullable', 'string', 'max:20'],
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in($validGenders)],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['sometimes', Rule::in($validStatuses)],
            'photo' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the school ID for validation.
     */
    protected function getSchoolId(): int
    {
        return $this->user()->school_id ?? $this->input('school_id');
    }
}
