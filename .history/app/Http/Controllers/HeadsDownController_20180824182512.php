<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Zttp\Zttp;
use function GuzzleHttp\json_encode;

class HeadsDownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::where('slack_id', $request->user_id)->first();

        if ($user) {
            $response = Zttp::get('https://slack.com/api/users.profile.get');

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

            return "You're now heads down for 60 minutes!";
        } else {
            return 'Lost in space...';
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
