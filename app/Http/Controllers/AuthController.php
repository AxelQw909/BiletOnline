<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showPartnerRegister() { return view('auth.register_partner'); }
    public function showOrganizerRegister() { return view('auth.register_organizer'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return Auth::user()->role === 'partner' 
                ? redirect()->route('partner.profile') 
                : redirect()->route('organizer.profile');
        }

        return back()->withErrors(['email' => 'Неверные учетные данные.']);
    }

    public function registerPartner(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['company_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'partner',
            'company_name' => $data['company_name'],
            'address' => $data['address'],
            'phone' => $data['phone'],
        ]);

        Auth::login($user);
        return redirect()->route('partner.profile');
    }

    public function registerOrganizer(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['company_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'organizer',
            'company_name' => $data['company_name'],
            'phone' => $data['phone'],
        ]);

        Auth::login($user);
        return redirect()->route('organizer.profile');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}