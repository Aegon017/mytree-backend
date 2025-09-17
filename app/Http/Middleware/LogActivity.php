<?php
namespace App\Http\Middleware;

use App\Jobs\LogActivityJob;
use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Proceed with the request
        $response = $next($request);

        // Log activity after the response is generated
        $this->logActivity($request, $response);

        return $response;
    }

    protected function logActivity(Request $request, $response)
    {
        $userId = auth()->check() ? auth()->id() : null;
        $maxPayloadLength = 65535; // For TEXT fields, adjust based on your column type
        $responsePayload = substr($response->getContent(), 0, $maxPayloadLength);
        ActivityLog::create([
            'user_id' => $userId,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'request_payload' => json_encode($request->except(['password', 'password_confirmation'])), // Exclude sensitive data
            'response_payload' => $responsePayload,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        // Prepare data for logging
        // $logData = [
        //     'user_id' => $userId,
        //     'endpoint' => $request->path(),
        //     'method' => $request->method(),
        //     'request_payload' => json_encode($request->except(['password', 'password_confirmation'])),
        //     'response_payload' => $response->getContent(),
        //     'ip_address' => $request->ip(),
        //     'user_agent' => $request->header('User-Agent'),
        // ];
        // LogActivityJob::dispatch($logData);
    }
}
