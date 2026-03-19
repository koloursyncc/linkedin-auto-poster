<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinkedInLoginController extends Controller
{
    public function showLogin()
    {
        if (session('linkedin_panel_authed') === true) {
            return redirect()->route('linkedin.dashboard');
        }

        return view('linkedin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $correct = hash_equals(
            config('linkedin.panel_password', ''),
            $request->input('password')
        );

        if (!$correct || empty(config('linkedin.panel_password'))) {
            return back()->with('error', 'Incorrect password.');
        }

        $request->session()->put('linkedin_panel_authed', true);
        $request->session()->regenerate();

        return redirect()->route('linkedin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('linkedin_panel_authed');
        $request->session()->regenerate();

        return redirect()->route('linkedin.login');
    }
}
