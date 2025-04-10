<?php

namespace Modules\DashboardNGO\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckNgoSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->hasRole('NGO_HeadOffice') && Auth::user()->hasRole('NGO_RegionalManagment') && Auth::user()->hasRole('NGO_OperationOfficer') && empty(session('user_id'))) {
            Auth::logout();
            return redirect('login');
        }
        return $next($request);
    }
}
