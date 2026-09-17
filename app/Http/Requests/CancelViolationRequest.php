<?php

namespace App\Http\Requests;

use App\Models\Violation;
use Illuminate\Foundation\Http\FormRequest;

class CancelViolationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // Only Super Admin and School Admin can cancel violations
        return $user->isSuperAdmin() || $user->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
