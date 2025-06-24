<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow users with role 'super_admin'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->guard('admin')->user();

        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Access denied: Super Admins only.');
        }

        return $next($request);
    }
}
