<?php

namespace App\Http\Requests;

use App\Models\PksDutyLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePksDutyLocationRequest extends FormRequest
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
        $pksDutyLocation = $this->route('pks_duty_location');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('pks_duty_locations')->where(function ($query) use ($pksDutyLocation) {
                    $query->where('school_id', $pksDutyLocation->school_id);
                })->ignore($pksDutyLocation),
            ],
            'status' => ['sometimes', Rule::in([PksDutyLocation::STATUS_ACTIVE, PksDutyLocation::STATUS_INACTIVE])],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'Kode lokasi sudah digunakan di sekolah ini.',
        ];
    }
}
