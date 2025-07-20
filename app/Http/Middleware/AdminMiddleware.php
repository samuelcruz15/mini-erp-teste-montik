<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        if (!auth()->user()->is_admin) {
            return redirect()->route('home')->with('error', 'Acesso negado. Área restrita para administradores.');
        }

        return $next($request);
    }
}
