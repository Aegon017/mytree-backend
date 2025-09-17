<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('areas')->insert(array(
            [
                'name' => "Madhapur",
                'slug' => 'madhapur',
                'city_id' => 1,
                'main_img' => '',
            ],
        ));
    }
}
