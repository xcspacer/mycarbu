<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->canManageSystem()) {
            return redirect('/dashboard')->with('error', 'Não tem permissão para aceder a esta página.');
        }

        return $next($request);
    }
}