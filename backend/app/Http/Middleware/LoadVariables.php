<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LoadVariables
{
    public function handle(Request $request, Closure $next)
    {
        // Add any global variables you want to share with views
        view()->share('appName', config('app.name'));
        
        return $next($request);
    }
} 