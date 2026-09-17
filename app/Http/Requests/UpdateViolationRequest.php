<?php

namespace App\Http\Requests;

use App\Models\Violation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateViolationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Only Super Admin and School Admin can update
        return $user->isSuperAdmin() || $user->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $violation = $this->route('violation');

        // Only recorded violations can be edited
        if ($violation && !$violation->canEdit()) {
            return [];
        }

        $schoolId = $violation->school_id;

        return [
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'evidences' => ['nullable', 'array', 'max:5'],
            'evidences.*' => [
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    /**
     * Check if request is empty (violation is not editable).
     */
    public function validationData(): array
    {
        $violation = $this->route('violation');

        if ($violation && !$violation->canEdit()) {
            return [];
        }

        return $this->all();
    }
}
