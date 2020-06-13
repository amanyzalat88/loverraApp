<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

use Closure;

class checkDemoVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        View::share('demo', [
            "demo_notification" => (App::environment('demo') == true)?Config::get('constants.demo_notification'):''
        ]);

        return $next($request);
    }
}
