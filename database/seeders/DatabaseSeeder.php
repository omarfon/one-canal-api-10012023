<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DocumentTypeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(BusinessSeeder::class);
        $this->call(FeeSeeder::class);
        $this->call(ReasonSeeder::class);
        $this->call(StatisticsSeeder::class);
    }
}
