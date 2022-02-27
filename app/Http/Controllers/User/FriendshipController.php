<?php

namespace App\Http\Controllers;

use App\Http\Resources\FriendsCollection;
use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\UserCollection;
use Pusher\Pusher;


class FriendshipController extends Controller
{

    public function userFriendRequest(Request $request){
        $user = User::find($request->sender);
        $recipient = User::find($request->recipient);

        $user->befriend($recipient);

        $friendRequest = $recipient->getFriendRequests();

        $content = [];
        foreach($friendRequest as $fq){
            array_push($content, User::find($fq->sender_id)->only(['id', 'firstname', 'lastname', 'username', 'profile_image']));
        }

        $options = array('cluster' => 'eu','useTLS' => true);
        $pusher = new Pusher('d2d1198b3b73bcc1343e', '150cc05ab4a99528094a', '763433', $options);
        $pusher->trigger($recipient->username.'_notifications', 'friends', end($content));

        return response()->json(['message' => "Friend request sent"]);
    }

    public function acceptedFriendRequest($id){
        $user = auth('api')->user();
        $sender = User::find($id);
        $user->acceptFriendRequest($sender);

        // $options = array('cluster' => 'eu','useTLS' => true);
        // $pusher = new Pusher('d2d1198b3b73bcc1343e', '150cc05ab4a99528094a', '763433', $options);
        // $pusher->trigger($sender->username.'_notifications', 'friends', $user);

        return response()->json(['message'=> 'Added to friend list']);
    }

    public function denyFriendRequest($id){
        $user = auth('api')->user();
        $sender = User::find($id);
        $user->denyFriendRequest($sender);
        return [];
    }

    public function removeFriend($id){
        $user = auth('api')->user();
        $friend = User::find($id);
        $user->unfriend($friend);
        return [];
    }


    public function getMyFriends(){
        $users = auth('api')->user();
        if(count($users->getFriends())==0){
            return 'You don\'t have any friends.';
        }
        else{
            return $users->getFriends();
        }
    }
    public function getFriendsById($id){
        $users = User::find($id);
        if(count($users->getFriends())==0){
            return [];
        }
        else{
            return FriendsCollection::collection($users->getFriends())->sortBy('created_at');
        }
    }

    public function getFriendRequests(Request $request){
        $user = User::find($request->id);
        $friendRequest = $user->getFriendRequests();
        $data = [];
        foreach($friendRequest as $fq){
            array_push($data, User::find($fq->sender_id)->only(['id', 'firstname', 'lastname', 'username', 'profile_image']));
        }
        return array_values(array_unique($data, SORT_REGULAR));

    }

    public function isFriendWith($friend_id){
        $user = auth('api')->user();
        $friend = User::find($friend_id);
        if($user->isFriendWith($friend)){
           return  "You are friend with $friend->username.";
        }
        else{
            return  "You aren't friend with $friend->username.";
        }
    }

    public function hasFriendRequestFrom($friend_id){
        $user = auth('api')->user();
        $friend = User::find($friend_id);
        if($user->hasFriendRequestFrom($friend)){
           return  "You have friend request from  $friend->username.";
        }
        else{
            return  "You don't have a friend request from $friend->username.";
        }
    }

    public function hasSentFriendRequestTo($friend_id){
        $user = auth('api')->user();
        $friend = User::find($friend_id);
        if($user->hasSentFriendRequestTo($friend)){
            return  "You sent a friend request to  $friend->username.";
        }
        else{
            return  "You didn't sent a friend request to $friend->username.";
        }
    }

    public function cancelSendFriendRequest($friend_id){
        $user = auth('api')->user();
        $friend = User::find($friend_id);
        if ($user->hasSentFriendRequestTo($friend)){
            $friend->denyFriendRequest($user);
            return [];
        }
    }
}
