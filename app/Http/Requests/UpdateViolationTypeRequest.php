<?php

namespace App\Http\Requests;

use App\Models\ViolationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViolationTypeRequest extends FormRequest
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
        $violationType = $this->route('violation_type');
        $validSeverities = [
            ViolationType::SEVERITY_LOW,
            ViolationType::SEVERITY_MEDIUM,
            ViolationType::SEVERITY_HIGH,
            ViolationType::SEVERITY_CRITICAL,
        ];

        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('violation_types')->where(function ($query) use ($violationType) {
                    $query->where('school_id', $violationType->school_id)
                          ->where('id', '!=', $violationType->id);
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'severity' => ['required', Rule::in($validSeverities)],
            'points' => ['required', 'integer', 'min:0', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
