<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('users')->insert([
            'names' => "Usuario",
            'surnames' => "Adminsitrador",
            'email' => "admin@onecanal.pe",
            'role' => "admin",
            'active' => 1,
            'password'   => bcrypt('admin123'),
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
