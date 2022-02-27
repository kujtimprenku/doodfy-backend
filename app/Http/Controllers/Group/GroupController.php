<?php

namespace App\Http\Controllers;

use App\Activity;
use App\User;
use App\Group;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // return (Group::all());

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $group = Group::create([
            'user_id' => auth('api')->user()->id,
            'city_id' => $request->city_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'group_type' => $request->group_type,
            'cover_image' => 'http://doodfy.ch/uploads/company/noimage.jpg',
        ]);
        return  response()->json(['message' => 'Created Successfully', 'id' => $group->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    // public function show(Group $group)
    // {
    //     return new GroupResource($group);
    // }

    /**
     * Display the specified with type resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function groupDetail($id, $type)
    {
        return new GroupResource(Group::where('id', $id)->where('group_type', $type)->firstOrFail());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        if ($request->image != '')
        {
            $uri =  $request->image;
            $img = explode(',', $uri);
            $ini =substr($img [0], 11);
            $extension = explode(';', $ini);
            $imageName = str_random(30).'.'. $extension [0];
            Storage::disk('local')->put('group/'. $imageName, fopen($request->image, 'r+'));
            $imageName = 'http://doodfy.ch/uploads/group/' .  $imageName;
            $group = Group::findOrFail($group->id);
            $group->cover_image = $imageName;
            $group->update();
            return response()->json(['message' => 'Updated Successfully']);
        }
        else
        {
            $group = Group::findOrFail($group->id);
            $group->name = $request->name;
            $group->description = $request->description;
            $group->facebook_url = $request->facebook_url;
            $group->instagram_url = $request->instagram_url;
            $group->twitter_url = $request->twitter_url;
            $group->update();
            return response()->json(['message' => 'Updated Successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response()->json([
            'message' => 'Delete Successfully'
            ]);
    }


    public function requestMembership(Request $request)
    {
        $user = auth('api')->user();
        $group = Group::find($request->group_id);
        $user->requestMembership($group);
        return response()->json(['message' => 'Sent successfully']);
    }


    public function getPendingMemberships($group_id)
    {
        $group = Group::find($group_id);
        $result = $group->getPendingMemberships();
        $users_id = $result->pluck('sender_id');
        $pending_users = User::whereIn('id', $users_id)
            ->get()
            ->each(function($item) use($group_id){
                $item->setAttribute('group_id', $group_id);
            });

        return MemberResource::collection($pending_users);
    }


    public function acceptMembershipRequest(Request $request)
    {
        $user = User::find($request->user_id);
        $group = Group::find($request->group_id);
        $result = $group->acceptMemberRequest($user);

        if($result) {
            return response()->json(['message' => 'Accepted successfully']);
        }
    }


    public function getAcceptedMemberships($group_id)
    {
        $group = Group::find($group_id);
        $result = $group->getAcceptedMemberships();
        $users_id = $result->pluck('sender_id');
        $accepted_users = User::whereIn('id', $users_id)
            ->get()
            ->each(function ($item) use($group_id){
                $item->setAttribute('group_id', $group_id);
            });

        return MemberResource::collection($accepted_users);
    }


    public function cancelMembership(Request $request)
    {
        $user = User::find($request->user_id);
        $group = Group::find($request->group_id);
        $result = $group->cancelMemberRequest($user);

        if($result) {
            return response()->json(['message' => 'Removed successfully']);
        }
    }


    public function leaveMembership(Request $request){
        $user = auth('api')->user();
        $group = Group::find($request->group_id);

        $result = $group->cancelMemberRequest($user);

        if($result) {
            return response()->json(['message' => 'Left successfully']);
        }
    }


    public function userMemberships($group_type)
    {
        $membership = DB::table('memberships')->where('sender_id', auth('api')->user()->id)
            ->where('status_id', 1)
            ->get()
            ->unique('recipient_id')
            ->pluck('recipient_id');

        return GroupResource::collection(Group::find($membership))
            ->filter(function($item) use($group_type){
                return $item->group_type == $group_type;
            })->values();
    }


    public function myGroups($type)
    {
        $my_groups = Group::where('user_id', auth('api')->user()->id)
            ->where('group_type', $type)
            ->get();
        return GroupResource::collection($my_groups);
    }

    public function groupActivities($group_id)
    {
        $activities_id = DB::table('group_activity')
            ->where('group_id', $group_id)
            ->get()
            ->pluck('activity_id');

        return ActivityResource::collection(Activity::find($activities_id));
    }
}
