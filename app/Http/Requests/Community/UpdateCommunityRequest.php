<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommunityRequest extends FormRequest
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
        $communityId = $this->route('community'); // Assuming the route parameter is named 'community'

        return [
            'name'     => ['required', 'string', 'max:255', Rule::unique('communities')->ignore($communityId)],
            'about'    => 'nullable|string',
            'location' => 'required|string|max:255'
        ];
    }
}
