<?php

namespace App\Http\Controllers;

use App\Models\LogSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            LogSistema::registrar('login_falha', 'Tentativa de login falhou para ' . $request->email . '.');
            return back()->withErrors(['email' => 'E-mail ou senha incorretos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        LogSistema::registrar('login_sucesso', 'Login realizado por ' . auth()->user()->nome . ' (' . $request->email . ').');

        $perfil = auth()->user()->perfil;

        if ($perfil === 'maquina') {
            return redirect()->route('maquina.index');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        LogSistema::registrar('logout', 'Logout realizado.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
