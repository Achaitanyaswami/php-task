<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service_request;
class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        \Log::info("Cron is working fine!");

        $max_date = date('Y-m-d H:i:s', strtotime("-1 days"));
        Service_request::where('created_at','<=',$max_date)
                       ->where('status',2)
                       ->delete();
        $result_arr['success']  = 1;

        $this->info('Demo:Cron Command Run successfully!');
    }
}
