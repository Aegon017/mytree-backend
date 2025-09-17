<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LocationsAddEditRequest extends FormRequest
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
        $id = $this->route('location'); // Assuming the route parameter is named 'location'

        return [
            'state_id' => 'required',
            'city_id'  => 'required',
            'area_id'  => [
                'required',
                'unique:tree_locations,area_id,' . $id . ',id,state_id,' . $this->state_id . ',city_id,' . $this->city_id,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Illuminate\Foundation\Http\FormRequest::messages()
     */
    public function messages()
    {
        return [
            'state_id.required' => 'State is required.',
            'city_id.required'  => 'City is required.',
            'area_id.required'  => 'Area is required.',
            'area_id.unique'    => 'This Tree location is already added',
        ];
    }
}
