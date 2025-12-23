<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;

class RegisterController extends Controller
{
    public function register()
    {
        return view('register');
    }
    
    public function actionregister(Request $request)
    {
        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
            
        ]);

        Session::flash('message', 'Register Berhasil. Akun Anda sudah Aktif silahkan Login menggunakan email dan password.');
        return redirect('register');
    }
    
}
