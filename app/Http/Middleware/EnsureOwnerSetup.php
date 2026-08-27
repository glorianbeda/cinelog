<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        if (User::count() === 0 && ! $request->routeIs('setup.*')) {
            return redirect()->route('setup.show');
        }

        return $next($request);
    }
}
