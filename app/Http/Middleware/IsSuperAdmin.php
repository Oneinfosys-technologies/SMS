<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty(auth()->check())) {
            return $next($request);
        }

        if (! \Auth::user()->is_default) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('user.errors.permission_denied')], 403);
            } else {
                abort(403, trans('user.errors.permission_denied'));
            }
        }

        return $next($request);
    }
}
