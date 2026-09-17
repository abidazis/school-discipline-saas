<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademicYearRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('academic_years')->where(function ($query) {
                    $query->where('school_id', $this->getSchoolId());
                }),
            ],
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
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
