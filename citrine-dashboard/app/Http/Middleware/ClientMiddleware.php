<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'client') {
            return $next($request);
        }

        if (auth()->user()->role === 'admin') {
            return redirect('/dashboard')->with('error', 'Admins cannot access the client portal.');
        }

        return redirect('/login');
    }
}
