<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(){
       $validated = request()->validate([
           'email' => 'required|email',
           'password' => 'required'
       ]);
       $credentials = request()->only('email', 'password');
       if (auth()->attempt($credentials)) {
           request()->session()->regenerate();
           return redirect('/');
       }
       return back()->withErrors([
           'email' => 'The provided credentials do not match our records.',
       ]);
    }

    public function logout(){
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    }
}
