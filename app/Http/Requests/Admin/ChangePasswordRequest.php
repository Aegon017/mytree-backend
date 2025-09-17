<?php

namespace App\Http\Requests\Admin;

use App\Rules\MatchOldPassword;
use App\Traits\FormatClientErrors;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    use FormatClientErrors;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password'          =>  ['required', new MatchOldPassword],
            'new_password'          =>  'required',
            'confirm_password'          =>  'required|same:new_password'
        ];
    }
}
