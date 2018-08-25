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

class EndSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $responseUrl;

    public function __construct($userId, $responseUrl = null)
    {
        $this->userId = $userId;
        $this->responseUrl = $responseUrl;
    }

    public function handle()
    {
        $user = User::where('slack_id', $this->userId)->first();

        logger('here');

        if(!$user) {
            return $this->sendResponse('User not found...');
        }

        logger('here');

        if(!$user->currentSession) {
            return $this->sendResponse("You aren't currently heads down!");
        }

        logger('here');

        $user->currentSession->update(['ended_at' => Carbon::now()]);

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
        if($this->responseUrl) {
            Zttp::post($this->responseUrl, compact('text'));
        }
    }
}
