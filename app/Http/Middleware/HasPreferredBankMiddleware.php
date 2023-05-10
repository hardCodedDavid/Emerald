<?php

namespace App\Http\Middleware;

use App\Bank;
use Closure;

class HasPreferredBankMiddleware
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
        if (auth()->user()->hasCompletedProfile() && auth()->user()->hasCompletedKinProfile()){
            $bank = Bank::where('user', auth()->user()['email'])->where('account_name', auth()->user()['name'])->first();
            if (!$bank){
                return redirect('/banks')->with('info', 'Please set a preferred bank to proceed');
            }
        }
        return $next($request);
    }
}
