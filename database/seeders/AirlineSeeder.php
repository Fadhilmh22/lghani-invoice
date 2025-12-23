<?php

namespace Database\Seeders;

use App\Models\Airlines;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Airlines::create([
            'airlines_code'=>'JT',
            'airlines_name'=>'Lion Air',
        ]);

        Airlines::create([
            'airlines_code'=>'JT',
            'airlines_name'=>'Lion Air',
        ]);

        Airlines::create([
            'airlines_code'=>'IP',
            'airlines_name'=>'Pelita Air',
        ]);

        Airlines::create([
            'airlines_code'=>'IW',
            'airlines_name'=>'Wings Air',
        ]);

        Airlines::create([
            'airlines_code'=>'UI',
            'airlines_name'=>'Super Air Jet',
        ]);

        Airlines::create([
            'airlines_code'=>'ID',
            'airlines_name'=>'Batik Air',
        ]);

        Airlines::create([
            'airlines_code'=>'GA',
            'airlines_name'=>'Garuda Indonesia',
        ]);

        Airlines::create([
            'airlines_code'=>'QG',
            'airlines_name'=>'Citilink',
        ]);

        Airlines::create([
            'airlines_code'=>'SJ',
            'airlines_name'=>'Sriwijaya Air',
        ]);

        Airlines::create([
            'airlines_code'=>'IN',
            'airlines_name'=>'NAM Air',
        ]);

        Airlines::create([
            'airlines_code'=>'QZ',
            'airlines_name'=>'Air Asia',
        ]);

        Airlines::create([
            'airlines_code'=>'XN',
            'airlines_name'=>'Xpress Air',
        ]);

        Airlines::create([
            'airlines_code'=>'IL',
            'airlines_name'=>'Trigana Air',
        ]);

        Airlines::create([
            'airlines_code'=>'8B',
            'airlines_name'=>'Trans Nusa',
        ]);


    }
}
