<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'=>'Fadhiil Mursyid Habibi',
            'email'=>'fadhilmursyidhabibi@gmail.com',
            'role'=>'Staff',
            'password'=>Hash::make('admin123'),
        ]);

        User::create([
            'name'=>'Indriawati',
            'email'=>'lghani_travel@ymail.com',
            'role'=>'Admin',
            'password'=>Hash::make('admin123'),
        ]);
    }
}