<?php

namespace App\Http\Controllers\Admin;

use App\Consts\AccountConst;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'max:' . AccountConst::EMAIL_LENGTH_MAX],
            'password' => ['required', 'string', 'max:' . AccountConst::PASSWORD_LENGTH_MAX],
        ]);
 
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->intended('/admin/dashboard');
        }
 
        return back()->withErrors([
            'root' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Sign out
     */
    public function signOut(Request $request): RedirectResponse
    {
        Auth::logout();
 
        return redirect()->intended('/home');
    }
}
