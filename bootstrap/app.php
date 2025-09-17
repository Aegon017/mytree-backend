<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
     
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
        // function (Router $router) {
        //     $router->middleware('web')
        //         ->group(base_path('routes/web.php'));

        //     $router->middleware('web')
        //         // ->namespace('Admin')
        //         ->prefix('Admin')
        //         // ->prefix('admin')
        //         ->group(base_path('routes/admin.php'));
        // },
        // commands: __DIR__ . '/../routes/console.php',
        // web: __DIR__ . '/../routes/web.php',
        // commands: __DIR__ . '/../routes/console.php',
        // health: '/up',
        // then: function () {
        //     \Route::namespace('Admin')->prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
        // },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('api', \App\Http\Middleware\ApiReqLog::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\LogActivity::class);
        // $middleware->appendToGroup('web', Spatie\Permission\Middleware\PermissionMiddleware::class);
        // $middleware->appendToGroup('web',  PermissionMiddleware::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    
    // ->withMiddleware(function (Middleware $middleware) {
    //     // $middleware->appendToGroup('api', \App\Http\Middleware\ApiReqLog::class);
    //     $middleware->appendToGroup('web',  PermissionMiddleware::class);
    // })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'status' => false,
                'message' => trans('user.unauthenticated'),
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        });
    })
    ->create();
