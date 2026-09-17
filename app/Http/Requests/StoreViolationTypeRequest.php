<?php

namespace App\Http\Requests;

use App\Models\ViolationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreViolationTypeRequest extends FormRequest
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
        $validSeverities = [
            ViolationType::SEVERITY_LOW,
            ViolationType::SEVERITY_MEDIUM,
            ViolationType::SEVERITY_HIGH,
            ViolationType::SEVERITY_CRITICAL,
        ];

        $validCategories = [
            ViolationType::CATEGORY_ATTENDANCE,
            ViolationType::CATEGORY_UNIFORM,
            ViolationType::CATEGORY_BEHAVIOR,
            ViolationType::CATEGORY_SAFETY,
            ViolationType::CATEGORY_ACADEMIC,
            ViolationType::CATEGORY_TECHNOLOGY,
            ViolationType::CATEGORY_LEAVING_SCHOOL,
            ViolationType::CATEGORY_OTHER,
        ];

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('violation_types')->where(function ($query) {
                    $query->where('school_id', $this->getSchoolId());
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in($validCategories)],
            'severity' => ['required', Rule::in($validSeverities)],
            'points' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
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
