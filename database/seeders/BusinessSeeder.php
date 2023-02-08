<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('businesses')->insert([
            'name' => "Arunta Holding SAC",
            'ruc' => "20607504076",
            'active' => 1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
