<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showPartnerRegister() { return view('auth.register_partner'); }
    public function showOrganizerRegister() { return view('auth.register_organizer'); }

    // --- ЛОГИКА ВОССТАНОВЛЕНИЯ ПАРОЛЯ ---
    public function showForgotPassword() { return view('auth.forgot-password'); }

    public function sendResetLink(Request $request) 
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $token = Str::random(64);
        
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Отправка письма (используйте ваш Mailable или просто Mail::raw)
        Mail::raw("Для смены пароля перейдите по ссылке: $resetLink", function ($message) use ($request) {
            $message->to($request->email)->subject('Восстановление пароля');
        });

        return back()->with('success', 'Ссылка для сброса пароля отправлена на вашу почту.');
    }

    public function showResetForm(Request $request, $token) 
    { 
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]); 
    }

    public function resetPassword(Request $request) 
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Неверный или просроченный токен.']);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Пароль успешно изменен!');
    }
    // ------------------------------------

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