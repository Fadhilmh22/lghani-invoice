<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;
use Session;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('home');
        }else{
            return view('login');
        }
    }

    public function actionlogin(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::Attempt($data)) {
            app(ActivityLogger::class)->record(
                $request,
                'login_success',
                'Login berhasil',
                Auth::user(),
                'User',
                Auth::id()
            );
            return redirect('home');
        }else{
            app(ActivityLogger::class)->record(
                $request,
                'login_failed',
                'Percobaan login gagal untuk email ' . $data['email'],
                null,
                'User',
                null,
                $data['email'],
                $data['email']
            );
            Session::flash('error', 'Email atau Password Salah');
            return redirect('/');
        }
    }

    public function actionlogout()
    {
        app(ActivityLogger::class)->record(
            request(),
            'logout',
            'Logout berhasil',
            Auth::user(),
            'User',
            Auth::id()
        );
        Auth::logout();
        return redirect('/');
    }
}
