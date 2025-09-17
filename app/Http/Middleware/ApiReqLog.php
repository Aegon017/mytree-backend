<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiReqLog
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!empty($request->header('Accept')) && $request->getMethod()!== 'GET'){
             if($request->header('Accept')!='application/json'){
                    return $this->error('Header Aceept only application/json', Response::HTTP_BAD_REQUEST);
                }
            }
        return $next($request);
    }
}
