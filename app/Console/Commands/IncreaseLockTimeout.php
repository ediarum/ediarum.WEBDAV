<?php

namespace App\Console\Commands;

use App\Models\Lock;
use Illuminate\Console\Command;

class IncreaseLockTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:increase-lock-timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Lock::where("timeout", 1800)
            ->update(["timeout" => 1000000000]);
        //
    }
}
