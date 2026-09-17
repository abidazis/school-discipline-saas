<?php

namespace App\Http\Requests;

use App\Models\PksMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksMemberRequest extends FormRequest
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
        $pksMember = $this->route('pks_member');

        return [
            'position' => ['sometimes', 'string', 'max:50'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'graduated', 'resigned'])],
            'joined_at' => ['sometimes', 'date'],
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
            'ended_at.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal bergabung.',
        ];
    }
}
