<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SignUpController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
        ]);

        $userRepository = new UserRepository();

        $user = $userRepository->create($request->name, $request->email, Hash::make($request->password));

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/home');
    }
}
