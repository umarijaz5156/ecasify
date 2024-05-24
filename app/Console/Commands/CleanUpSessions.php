<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUpSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup';
    protected $description = 'Clean up old sessions from the sessions table';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('sessions')
            ->where('last_activity', '<', now()->subHours(12)) 
            ->delete();

        $this->info('Sessions cleanup completed.');
    }
}
