<?php

namespace App\Http\Controllers;

use App\Chat;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Arr;

class ChatController extends Controller
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
        $message  = Chat::create([
            'sender_id' => auth('api')->user()->id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'read' => 0
        ]);

        return 'message send successfully';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chat $chat)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chat $chat)
    {
        //
    }


    public function getFriendMessage($id){
        $sender_id = auth('api')->user()->id;
        $my_messages = Chat::where('sender_id', $sender_id)->where('receiver_id', $id)
        ->get();
        $array = [];
        foreach($my_messages as $my_message){
            array_push($array, $my_message->setAttribute('my_comment', $my_message->sender_id===$sender_id));
        }
        $friend_messsages = Chat::where('receiver_id', $sender_id)->where('sender_id', $id)
        ->get();
        $array2 = [];
        foreach($friend_messsages as $friend_messsage){
            array_push($array2, $friend_messsage->setAttribute('my_comment', $friend_messsage->sender_id===$sender_id));
        }
        $data = collect(array_merge($array, $array2))->sortByDesc('created_at')->reverse()->values();
        return $data;
    }

    public function getLastFriendMessage(){
        $sender_id = auth('api')->user()->id;
        $my_last_messages = Chat::where('sender_id', $sender_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->unique('receiver_id')
            ->values();

        $array = [];
        foreach($my_last_messages as $my_last_message){
            array_push($array, User::find($my_last_message->receiver_id)
            ->setAttribute('message', $my_last_message->content)
            ->setAttribute('created', \Carbon\Carbon::createFromTimeStamp(strtotime($my_last_message->created_at))->diffForHumans())
            ->only(['id', 'username', 'profile_image', 'message', 'created'])
            );
        }

        $receiver_id = auth('api')->user()->id;
        $friend_last_messages = Chat::where('receiver_id', $receiver_id)
            ->orderBy('id', 'DESC')
            ->get()
            ->unique('sender_id')
            ->values();
        $array2 = [];
        foreach($friend_last_messages as $friend_last_message){
            array_push($array2, User::find($friend_last_message->sender_id)
            ->setAttribute('message', $friend_last_message->content)
            ->setAttribute('created', \Carbon\Carbon::createFromTimeStamp(strtotime($friend_last_message->created_at))->diffForHumans())
            ->only(['id', 'username', 'profile_image', 'message', 'created'])
            );
        }

        $data = collect(array_merge($array, $array2))->unique('id')->sortByDesc('created_at')->values();
        return $data;
    }
}
