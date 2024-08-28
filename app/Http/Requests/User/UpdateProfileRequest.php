<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'birth_date'   => 'required|date|date_format:Y-m-d|before:today',
            'gender'       => 'nullable|in:male,female,common',
            'language'     => 'nullable|string|max:255',
            'location'     => 'nullable|string|max:255',
            'address'      => 'nullable',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'gender.in' => 'The selected gender is invalid. It must be one of the following: male, female, common.',
        ];
    }
}
