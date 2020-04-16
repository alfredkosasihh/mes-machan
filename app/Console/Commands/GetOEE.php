<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\OEEperformanceController;
class GetOEE extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OEE-data:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get OEEdata';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OEEperformanceController $OEEperformanceController)
    {
        parent::__construct();
        $this->OEEperformanceController = $OEEperformanceController;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->OEEperformanceController->getOEEperformance();
    }
}
