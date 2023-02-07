<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use Auth;
use DB;
use Artisan;

class CheckPermissions
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
        $i = 1;

        do {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            
            $i++;
        } while($i <= 10);

        // cache()->flush();

        if(Auth::check() && Auth::user()->role == '1') 
        {            
            return $next($request);
        } 
        else if(Auth::check() && Auth::user()->role == '2')
        {
            $current_function = Route::currentRouteAction();

            $user = DB::table('users')->where('id', Auth::user()->id)->first();
            $permissions = explode(',', $user->permissions);

            if( in_array( class_basename($current_function) , $permissions) or Auth::user()->permissions == '*')
            {
                return $next($request);
            }
            else {
                return redirect()->action('DashboardController@index')->with('danger',"You cannot access this page");
            }
        }
    }
}
