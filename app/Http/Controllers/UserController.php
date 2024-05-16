<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login()
    {
        $validated = request()->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        $credentials = request()->only('username', 'password');
        if (auth()->attempt($credentials)) {
            request()->session()->regenerate();
            $role = auth()->user()->role;
            if ($role == '0') {
                return redirect('/');
            } elseif ($role == '1') {
                return redirect('/approve');
            } elseif ($role == '2') {
                return redirect('/get-posts');
            }
        }
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    }
}
