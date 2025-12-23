<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'booker'=>'Fadhiil Mursyid Habibi',
            'phone'=>'085867887525',
            'email'=>'fadhilmursyidhabibi@gmail.com',
            'payment'=>'Cash',
        ]);
    }
}
