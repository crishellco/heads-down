<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use Zttp\Zttp;

class EndSession implements ShouldQueue
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

        if($user->currentSession) {
            return $this->sendResponse("You aren't currently heads down!");
        }

        $endDndParams = [
            'token' => $user->slack_token,
        ];

        $setStatusParams = [
            'profile' => json_encode([
                'status_emoji' => $user->currentSession->status_emoji,
                'status_text' => $user->currentSession->status_text,
            ]),
            'token' => $user->slack_token,
        ];

        Zttp::get('https://slack.com/api/dnd.endDnd', $endDndParams);
        Zttp::get('https://slack.com/api/users.profile.set', $setStatusParams);

        return $this->sendResponse("You're now heads up!");
    }

    protected function sendResponse($text)
    {
        Zttp::post($this->request['response_url'], compact('text'));
    }
}
