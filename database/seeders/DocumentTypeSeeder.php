<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('document_types')->delete();

        DB::table('document_types')->insert([
            [
                'id'         => 1,
                'name'       => 'Documento Nacional de Identidad',
                'short_name'  => 'DNI',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 2,
                'name'       => 'Carnet de Extranjería',
                'short_name'  => 'CE',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ], [
                'id'         => 3,
                'name'       => 'Pasaporte',
                'short_name'  => 'PAS',
                'active'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
