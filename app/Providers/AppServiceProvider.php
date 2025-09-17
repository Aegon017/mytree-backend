<?php

namespace App\Providers;

use App\Models\Admin\FollowUp;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('Admin.commons.sidebar',function ($view) {
            $roles= DB::table('roles')->get();
            $view->with('rolesList',$roles);
        });
        RedirectIfAuthenticated::redirectUsing(function () {
            return route('dashboard');
        });
    }
}
