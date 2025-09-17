<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlotDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('slot_durations')->insert(array(
            [
                'duration' => "9:30 AM - 12:30 PM",
            ],
            [
                'duration' => "1:00 PM - 4:00 PM",
            ],
            [
                'duration' => "4:30 PM - 6:00 PM",
            ],
            [
                'duration' => "6:30 PM - 9:30 PM",
            ],
            [
                'duration' => "10:00 PM - 1:00 AM",
            ],
        ));
    }
}
