<?php

namespace App\Http\Middleware;

use Closure;

class CustomerCareMiddleware
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
        if ($request->user() && !$request->user()->hasRole('customer_care')) {
            return redirect('unauthorized');
        }
        return $next($request);
    }
}
