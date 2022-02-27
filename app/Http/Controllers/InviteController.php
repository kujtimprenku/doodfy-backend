<?php

namespace App\Http\Controllers;

use App\Activity as AppActivity;
use App\ActivityUser;
use App\Group;
use App\Http\Resources\Activity;
use App\Http\Resources\InviteResource;
use App\Notifications\InviteFriendsToGroup;
use App\User;
use Illuminate\Http\Request;

class InviteController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    public function sendInvite(Request $request)
    {
        $sender = auth('api')->user();
        $group = Group::find($request->group_id);
        $recipient = User::find($request->recipient_id);
        $sender->sendInvite($recipient, $group);
        $recipient->notify(new InviteFriendsToGroup($group));
        return response()->json(['message' => $recipient->username.' has been invited.']);
    }

    public function acceptInvite(Request $request)
    {
        $sender = User::find($request->sender_id);
        $recipient = User::find($request->recipient_id);
        $group = Group::find($request->group_id);
        $recipient->acceptInvite($sender, $group);
        $recipient->requestMembership($group);


    }

    public function inviteFriendsGroup($group_id){

        $user = auth('api')->user();
        $my_friends = $user->getFriends();
        return InviteResource::collection($my_friends)->each(function($item) use($group_id){
            $item->setAttribute('group_id', $group_id);
        });
    }

}
