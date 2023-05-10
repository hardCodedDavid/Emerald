<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class ManagerAdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check()) {
            if(Auth::user()->role != 2 && Auth::user()->role != 3){
                return redirect()->route('admin.home')->with('message', '<div class="alert alert-warning alert-dismissible show fade alert-has-icon"><div class="alert-icon"><i class="far fa-lightbulb"></i></div><div class="alert-body"><button class="close" data-dismiss="alert"><span>&times;</span></button>Not authorized to take action.</div></div>');
            }
        }
        return $next($request);

    }
}
