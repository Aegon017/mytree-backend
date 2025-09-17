<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use app\Helpers\ValidationHelper;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @category	Trait
 * @package		Rest Response
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

trait RestfulTrait
{
    protected function createdResponse($data, $message = '')
    {
        $response = [
            'code' => 201,
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($response, $response['code']);
    }

    protected function showAcceptedResponse($data, $message = '')
    {
        $response = [
            'code' => 202,
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($response, $response['code']);
    }

    protected function showSuccessResponse($data = [], $message = null)
    {
        $response = [
            'code' => 200,
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ];
        return response()->json($response, $response['code']);
    }

    protected function listResponse($data)
    {
        $response = [
            'code' => 200,
            'status' => 'success',
            'data' => $data
        ];
        return response()->json($response, $response['code']);
    }

    protected function notFoundResponse($message = "Not Found")
    {
        $response = [
            'code' => 404,
            'status' => 'error',
            'data' => 'Resource Not Found',
            'message' => $message
        ];
        return response()->json($response, $response['code']);
    }

    protected function notAuthorizedOrForbiddenResponse($message = 'Not Authorized', $code = 401)
    {
        $response = [
            'code' => $code,
            'status' => 'error',
            'data' => [],
            'message' => $message
        ];
        return response()->json($response, $response['code']);
    }

    protected function requestCompleted()
    {
        $response = [
            'code' => 204,
            'status' => 'success',
            'data' => [],
            'message' => 'Request processed'
        ];
        return response()->json($response, $response['code']);
    }

    protected function deletedResponse()
    {
        $response = [
            'code' => 204,
            'status' => 'success',
            'data' => [],
            'message' => 'Resource deleted'
        ];
        return response()->json($response, $response['code']);
    }

    protected function getFirstValidationErrorMessage(array $errors = [], $message = 'Please check your inputs', $priority = false): string
    {
        if ($priority) {
            return $message;
        }
        try {
            $firstErrors = head($errors);

            if (is_array($firstErrors)) {
                $message = head($firstErrors);
            }
            if (!is_string($message)) {
                $message = 'Please check your inputs';
            }
        } catch (\Exception $ex) {
            $message = 'Please check your inputs';
        }
        return $message;
    }

    protected function validationErrors(array $errors = [], $message = 'Please check your inputs', $exception = 'ValidationException', $priority = false)
    {
        $response = [
            'code'      =>  422,
            'status'    =>  'error',
            'data'      =>  [
                'errors'    =>  $errors,
            ],
            'exception' =>   $exception,
            'message'   =>  $this->getFirstValidationErrorMessage($errors, $message, $priority),
        ];
        return response()->json($response, 422);
    }

    protected function makeErrorResponse(array $errors = [], string $exception = 'ValidationException', $code = 422)
    {
        $data = ['errors' => $errors, 'exception' => $exception];
        return $this->clientErrorResponse($data, $code);
    }

    protected function error(string $message = null, int $code = 204, $data = null)
    {
        return response()->json([
            'code' => 204,
            'status' => false,
            'data' => [],
            'message' => $message
        ], 400);
    }
}
