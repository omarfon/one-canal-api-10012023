<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('fees')->delete();

        DB::table('fees')->insert([
            [
                'id'         => 1,
                'type'       => 'Fee',
                'value'  => '9',
                'tag'  => ' S/',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 2,
                'type'       => 'IGV',
                'value'  => '18',
                'tag'  => '%',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 3,
                'type'       => 'ITF',
                'value'  => '0.05',
                'tag'  => '%',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
