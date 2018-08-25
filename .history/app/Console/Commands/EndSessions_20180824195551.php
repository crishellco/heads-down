<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Session;
use Carbon\Carbon;
use App\Jobs\EndSession;

class EndSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End work sessions which have expired';

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
        // Get sessions which need to be ended
        // ended_at is null and Carbon::now() - started_at >= duration_in_minutes
        Session::whereNull('ended_at')->get()->each(function($session) {
            if($session->started_at->addMinutes($session->duration_in_minutes) <= Carbon::now()) {
                EndSession::dispatch($session->user_id);
            }
        });
    }
}
