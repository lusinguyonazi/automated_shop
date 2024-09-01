<?php

namespace App\Http\Middleware;

use Closure;

class JointVenture
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
        if ($request->user() && !$request->user()->hasRole('joint_vent_partner')) {
            return redirect('unauthorized');
        }
        return $next($request);
    }
}
