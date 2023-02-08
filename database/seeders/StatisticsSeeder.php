<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Helpers\Functions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatisticsSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('statistics')->delete();

        DB::table('statistics')->insert([
            [
                'id' => 1,
                'type' => 'login',
                'date' => date('Y-m-d'),
                'value' => 0,
                'tag' => Functions::convertDayName(date('N')),
                'verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id' => 2,
                'type' => 'salary-advances-amounts',
                'date' => date('Y-m-d'),
                'value' => 0,
                'tag' => Functions::convertDayName(date('N')),
                'verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id' => 3,
                'type' => 'salary-advances-numbers',
                'date' => date('Y-m-d'),
                'value' => 0,
                'tag' => Functions::convertDayName(date('N')),
                'verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
