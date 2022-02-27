<?php

namespace App\Http\Controllers;

use App\UserXpPoint;
use Illuminate\Http\Request;
use App\User;

class UserXpPointController extends Controller
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
    public function store(Request $request){
        $sender = User::find(auth('api')->user()->id);
        $recipient = User::find($request->user_id);
        if ($sender->xp_points > 0) {
            $sender->decrement('xp_points');
            UserXpPoint::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'activity_id' => $request->activity_id,
            ]);
            return response()->json(['message' => '1xp sent to '.$recipient->username]);
        }
        else {
            return response()->json(['message' => 'You don\'t have XP.']);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\UserXpPoint  $userXpPoint
     * @return \Illuminate\Http\Response
     */
    public function show(UserXpPoint $userXpPoint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserXpPoint  $userXpPoint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserXpPoint $userXpPoint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserXpPoint  $userXpPoint
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserXpPoint $userXpPoint)
    {
        //
    }


    public function giveXpToAll(Request $request){
        $message = '';
        foreach($request->users_id as $user_id){
            $sender = User::find(auth('api')->user()->id);
            $recipient = User::find($user_id);
            $count_users = count($request->users_id);
            $my_xp = $sender->xp_points;
            if ($my_xp > $count_users){
                $sender->decrement('xp_points');
                UserXpPoint::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'activity_id' => $request->activity_id
                ]);
                $message = '1xp sent to all';
            }
            else {
                $message = 'You don\'t have enough xp points';
            }
        }
        return response()->json(['message' => $message]);
    }


}


