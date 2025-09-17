<?php

namespace App\Traits;

use Illuminate\Http\Response;

/**
 * @category	Trait
 * @package		Api Response
 * @author		Harish Mogilipuri
 * @license
 * @link
 * @created_on
 */

trait ApiResponser
{
    protected function success($data,string $message = null, int $code = Response::HTTP_OK)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = null, int $code, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
