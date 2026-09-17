<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
        $department = $this->route('department');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departments')->where(function ($query) use ($department) {
                    $query->where('school_id', $department->school_id)
                          ->where('id', '!=', $department->id);
                }),
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
