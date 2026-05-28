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
            ->where('password', $request->password)
            ->first();

        if ($user) {

            session([
                'user' => $user
            ]);

            if ($user->role == 'admin') {

                return redirect('/rooms');

            } else {

                return redirect('/booking/perkuliahan');
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