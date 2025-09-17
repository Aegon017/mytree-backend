<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert(array(
            [
                'name' => "admin",
                'email' => 'admin@gmail.com',
                'password' => bcrypt('123456'),
            ]
        ));
    }
}
