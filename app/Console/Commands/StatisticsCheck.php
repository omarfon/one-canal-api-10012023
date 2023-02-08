<?php

namespace App\Console\Commands;

use App\Models\Statistic;
use App\Helpers\Functions;
use Illuminate\Console\Command;

class StatisticsCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check statistics for a new day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $statistics = [
            'login', 'salary-advances-amounts', 'salary-advances-numbers'
        ];

        foreach ($statistics as $key => $statistic) {
            Statistic::firstOrCreate(
                [
                    'date' => date('Y-m-d'),
                    'type' => $statistic
                ],
                [
                    'value' => 0,
                    'tag' => Functions::convertDayName(date('N'))
                ]
            );
        }

        \Log::info('Success: check statistics for a new day');
    }
}
