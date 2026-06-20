<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = User::where('nim_nip', $request->nim_nip)
            ->first();

        if ($user) {
            $isHashed = password_get_info($user->password)['algoName'] !== 'unknown';
            $passwordMatches = $isHashed 
                ? \Hash::check($request->password, $user->password) 
                : ($request->password === $user->password);

            if ($passwordMatches) {
                session([
                    'user' => $user
                ]);

                if ($user->role == 'admin') {
                    return redirect('/admin/dashboard');
                } else {
                    return redirect('/dashboard');
                }
            }
        }

        return back()->with('error', 'Login gagal');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}