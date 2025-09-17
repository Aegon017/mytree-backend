<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @category	Trait
 * @package		FormatClientErrors
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */
/**
 * Trait FormatClientErrors
 */

trait FormatClientErrors
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response   =   [];
        $message    =   null;

        if (property_exists($this, 'message')) {
            $message =   $this->message;
        }

        $errors = $validator->errors()->toArray();

        if ($this->expectsJson() || $this->ajax()) {
            $response = [
                'code'      =>  422,
                'status'    =>  'error',
                'data'      =>  [
                    'errors'    =>  $errors,
                ],
                "exception" =>   'ValidationException',
                'message'   =>  $this->getErrorMessageFromFirstError($errors, $message),
            ];
        } else {
            // $response = $errors;
            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
        throw new HttpResponseException(response()->json($response, 422));
    }

    protected function getErrorMessageFromFirstError(array $errors = [], $message): string
    {
        try {
            $message = $message ?? 'Please check your inputs';

            $firstErrors = head($errors);

            if (is_array($firstErrors)) {
                $firstError = head($firstErrors);
                if (is_string($firstError)) {
                    $message = $firstError;
                }
            }
        } catch (\Exception $ex) {
            $message = 'Please check your inputs';
        }
        return $message;
    }
}
