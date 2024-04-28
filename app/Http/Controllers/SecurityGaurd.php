<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Nullable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SecurityGaurd extends Controller
{
    //

    public function live()
    {
        return false;
    }

    public function password()
    {
        return '@3456025AQ%71GbghMi*8N46w%i^j7Gn^2dsdfghcu^w#p&';
    }

    public function check()
    {
        $allow = false;

        if (isset(apache_request_headers()['secure'])) {

            $pass = apache_request_headers()['secure'];
            $allow = $pass === $this->password();
            
            
        }
        if (isset(apache_request_headers()['Secure'])) {

            $pass = apache_request_headers()['Secure'];
            $allow = $pass === $this->password();
            
            
        }
        // if(auth()->user()!=null){
        //     if(auth()->user()->status==1){
        //         return false;
        //     }
        // }

        // Log::channel('callvcal')->info("moddleware " . " " . json_encode([
        //     'auth'=>auth()->user(),
        // ]));


        return $allow;
    }
}
