<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('banks')->delete();

        DB::table('banks')->insert([
            [
                'id'         => 1,
                'name'       => 'Banco de Crédito del Perú',
                'short_name'  => 'BCP',
                'photo'      => 'bcp.jpg',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 2,
                'name'       => 'Banco Internacional del Perú',
                'short_name'  => 'Interbank',
                'photo'      => 'interbank.jpg',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 3,
                'name'       => 'Scotiabank Perú',
                'short_name'  => 'Scotiabank',
                'photo'      => 'scotiabank_peru.jpg',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 4,
                'name'       => 'BBVA Continental',
                'short_name'  => 'BBVA',
                'photo'      => 'bbva_continental.jpg',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 5,
                'name'       => 'Banco Financiero del Perú',
                'short_name'  => 'Pichincha',
                'photo'      => 'banco_pichincha.jpg',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 6,
                'name'       => 'Banco Interamericano de Finanzas',
                'short_name'  => 'BanBif',
                'photo'      => 'banbif.jpg',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
