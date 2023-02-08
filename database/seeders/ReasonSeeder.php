<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('reasons')->delete();

        DB::table('reasons')->insert([
            [
                'id'         => 1,
                'name'  => 'Motivo 1',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 2,
                'name'  => 'Motivo 2',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 3,
                'name'  => 'Motivo 3',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
