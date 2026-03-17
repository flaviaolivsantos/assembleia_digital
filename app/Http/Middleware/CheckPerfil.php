<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPerfil
{
    // Hierarquia: perfis que têm acesso a cada nível
    private array $hierarquia = [
        'admin'      => ['admin'],
        'responsavel' => ['responsavel', 'admin'],
        'mesario'    => ['mesario', 'responsavel', 'admin'],
        'maquina'    => ['maquina'],
    ];

    public function handle(Request $request, Closure $next, string ...$perfis): Response
    {
        if (!auth()->check()) {
            abort(403, 'Acesso não autorizado.');
        }

        $perfilUsuario = auth()->user()->perfil;

        foreach ($perfis as $perfilNecessario) {
            $permitidos = $this->hierarquia[$perfilNecessario] ?? [$perfilNecessario];
            if (in_array($perfilUsuario, $permitidos)) {
                return $next($request);
            }
        }

        abort(403, 'Acesso não autorizado.');
    }
}
