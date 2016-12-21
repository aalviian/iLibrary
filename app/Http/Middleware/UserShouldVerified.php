<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class UserShouldVerified
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
        $response = $next($request);
        if(Auth::check() && !Auth::user()->is_verified) {
            Auth::logout();

            Session::flash("flash_notif", [
                "level" => "warning",
                "message" => "Akun anda belum aktif. Silahkan klik pada link aktivasi yang telah dikirim melalui Email."
            ]);

            return redirect('/login');
        }
        return $response;
    }
}
