<?php

namespace App\Jobs;

use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zttp\Zttp;

class BeginSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $request;

    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function handle()
    {
        $response = Zttp::get('https://slack.com/api/users.profile.get', ['token' => $this->user->slack_token]);
        $json = $response->json();

        $this->user->sessions()->create([
            'status_emoji' => $json['profile']['status_emoji'],
            'status_text' => $json['profile']['status_text'],
            'started_at' => Carbon::now(),
        ]);

        $setSnoozeParams = [
            'token' => $this->user->slack_token,
            'num_minutes' => 60,
        ];

        $setStatusParams = [
            'profile' => json_encode([
                'status_emoji' => ':red_circle:',
                'status_text' => 'Heads down!',
            ]),
            'token' => $this->user->slack_token,
        ];

        Zttp::get('https://slack.com/api/dnd.setSnooze', $setSnoozeParams);
        Zttp::get('https://slack.com/api/users.profile.set', $setStatusParams);

        Zttp::post($this->request->response_url, ['text' => "You're now heads down for 60 minutes!"]);
    }
}
