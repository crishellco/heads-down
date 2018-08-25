<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zttp\Zttp;
use App\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = [
            'client_id' => env('SLACK_APP_CLIENT_ID'),
            'client_secret' => env('SLACK_APP_CLIENT_SECRET'),
            'code' => $request->code,
        ];

        $response = Zttp::get('https://slack.com/api/oauth.access', $params);

        if($response->isSuccess()) {
            $json = $response->json();

            User::create([
                'slack_id' => $json['user_id'],
                'slack_token' => $json['access_token'],
            ]);

            return 'Success!';
        } else {
            return abort(401, 'Could not authenticate app...');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        logger('index');
        logger($request);
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
