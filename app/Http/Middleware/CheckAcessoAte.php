<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAcessoAte
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->acesso_ate && now()->isAfter($user->acesso_ate)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Seu acesso expirou em ' . $user->acesso_ate->format('d/m/Y \à\s H:i') . '. Entre em contato com o administrador.']);
        }

        return $next($request);
    }
}
