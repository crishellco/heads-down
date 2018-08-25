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

    protected $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $user = User::where('slack_id', $this->request['user_id'])->first();

        if(!$user) {
            return $this->sendResponse('User not found...');
        }

        if ($user) {
            $response = Zttp::get('https://slack.com/api/users.profile.get', ['token' => $user->slack_token]);
            $json = $response->json();

            $user->sessions()->create([
                'status_emoji' => $json['profile']['status_emoji'],
                'status_text' => $json['profile']['status_text'],
                'started_at' => Carbon::now(),
            ]);

            $setSnoozeParams = [
                'token' => $user->slack_token,
                'num_minutes' => 60,
            ];

            $setStatusParams = [
                'profile' => json_encode([
                    'status_emoji' => ':red_circle:',
                    'status_text' => 'Heads down!',
                ]),
                'token' => $user->slack_token,
            ];

            Zttp::get('https://slack.com/api/dnd.setSnooze', $setSnoozeParams);
            Zttp::get('https://slack.com/api/users.profile.set', $setStatusParams);

            $response = "You're now heads down for 60 minutes!";
        } else {
            $response = 'Lost in space...';
        }

        // Send delayed response


    }

    protected function sendResponse($text)
    {
        Zttp::post($this->request['response_url'], compact('text'));
    }
}
