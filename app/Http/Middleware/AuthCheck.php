<?php

namespace App\Http\Middleware;

use Closure;
use Func;

class AuthCheck
{
    public function handle($request, Closure $next)
    {
        return redirect('/');
    }
}