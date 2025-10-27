<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fname' => ['required', 'string', 'max:255'],
            'lname' =>['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_number' =>['required', 'regex:/^[0-9]{10}$/'],
            'postal_code' =>['required','string','regex:/^[0-9]{5}$/'],
            'distric' =>['required','string','max:255'],
            'province' =>['required', 'string', 'max:255'],
            'detail' =>['required', 'string', 'max:255'],
            'country' =>['required','string','max:255'],
        ];
    }
}
