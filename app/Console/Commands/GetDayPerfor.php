<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DayPerformanceStatisticsController;
class GetDayPerfor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dayPerfor-data:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get dayPerfor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DayPerformanceStatisticsController $dayPerformanceStatisticsController)
    {
        parent::__construct();
        $this->dayPerformanceStatisticsController = $dayPerformanceStatisticsController;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dayPerformanceStatisticsController->getmachineperformance();
    }
}
