<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
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
            'user_type'         =>  'required',
            'mobile_prefix'     =>  'required',
            'mobile'            =>  'required|unique:users',
            'fcm_token'         =>  'required|unique:users',
            // 'referral_code'     =>  'nullable|exists:users,referral_code',
            'referral_code'     =>  'nullable|string|exists:users,referral_code'

        ];
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Foundation\Http\FormRequest::messages()
     */
    public function messages()
    {
        return [];
    }
}
