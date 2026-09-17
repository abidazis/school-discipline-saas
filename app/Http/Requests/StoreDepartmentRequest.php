<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departments')->where(function ($query) {
                    $query->where('school_id', $this->getSchoolId());
                }),
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
