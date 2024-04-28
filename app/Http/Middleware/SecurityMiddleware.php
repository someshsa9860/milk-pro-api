<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SecurityGaurd;
use App\Http\Controllers\AdminController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $allow
            = (new SecurityGaurd())->check();

        if ($allow) {
            return $next($request);
        }
        return response(array('message' => "You don't have access to use app please contact owner"), 401);
    }
}
