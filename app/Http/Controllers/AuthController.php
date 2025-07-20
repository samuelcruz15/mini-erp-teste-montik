<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }
        
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user();
            $message = $user->is_admin ? 
                'Login realizado com sucesso! Bem-vindo(a), ' . $user->name . ' (Administrador)' :
                'Login realizado com sucesso! Bem-vindo(a), ' . $user->name;

            if ($user->is_admin) {
                return redirect()->route('admin.dashboard')->with('success', $message);
            } else {
                return redirect()->route('home')->with('success', $message);
            }
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }
}
