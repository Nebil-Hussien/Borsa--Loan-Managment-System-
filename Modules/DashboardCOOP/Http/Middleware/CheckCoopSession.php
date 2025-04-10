<?php

namespace Modules\DashboardCOOP\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCoopSession
{
  /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check() && Auth::user()->hasRole('COOP_HeadOffice') && Auth::user()->hasRole('COOP_RegionalManagment') && Auth::user()->hasRole('COOP_OperationOfficer') && empty(session('user_id'))) {
            Auth::logout();
            return redirect('login');
        }
        return $next($request);
    }
}
