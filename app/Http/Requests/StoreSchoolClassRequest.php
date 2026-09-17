<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\SchoolClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Super Admin and School Admin can create/update/delete
        if ($this->isMethod('POST') || $this->isMethod('PATCH') || $this->isMethod('DELETE')) {
            return $this->user()->isSuperAdmin() || $this->user()->isSchoolAdmin();
        }

        // All authenticated users can view
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolId = $this->getSchoolId();
        $validGrades = ['VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return [
            'academic_year_id' => [
                'required',
                'integer',
                Rule::exists('academic_years', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where(function ($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }),
            ],
            'name' => [
                'required',
                'string',
                'max:50',
            ],
            'grade_level' => [
                'required',
                'string',
                'max:10',
                Rule::in($validGrades),
            ],
            'is_active' => ['sometimes', 'boolean'],
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
