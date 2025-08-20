<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'name' => ['required', 'min:3', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:3']
            // Ive set the min password length to 3 since the user will be able to edit his/her credentials later
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        auth('web')->login($user);
        return redirect('/');
    }

    public function login(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (auth('web')->attempt(['name' => $incomingFields['username'],'password' => $incomingFields['password']])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors(['name' => 'Invalid username or password.']);
    }
    
    public function logout() {
        auth('web')->logout();
        return redirect('/');
    }
}
