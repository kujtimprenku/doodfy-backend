<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reaction;
use App\Http\Resources\CommentCollection;
use Pusher\Pusher;
use App\Activity;
use App\Notifications\CommentToYourActivity;

class ActivityCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //return CommentCollection::collection(Reaction::all());
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $create = Reaction::create(['user_id' => auth('api')->user()->id, 'activity_id' => $request->id, 'comment' => $request->comment]);

        $options = array('cluster' => 'eu', 'useTLS' => true);
        $pusher = new Pusher('d2d1198b3b73bcc1343e', '150cc05ab4a99528094a', '763433', $options);

        $comments = CommentCollection::collection(Reaction::where('activity_id',$request->id)->where('id', $create->id)->get());
        $pusher->trigger($request->id.'_comments', 'posts', $comments);

        $activity = Activity::find($request->id);

        $user = $activity->user;
        ($user->id != auth('api')->user()->id) ? $user->notify(new CommentToYourActivity($activity)) : null;


        $notify = $user->notifications[0]->data;
        $pusher->trigger($user->username.'_notifications', 'notifications', $notify);

        return $comments;
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
        $data = Reaction::find($id);
        if(strlen($data) > 0){
            $data->comment = $request->comment;
            $data->update();
            return response()->json(['message' => 'Updated Successfully']);
        }
        else{
            return response()->json(['message' => "Don't have comment with id $id."]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Reaction::find($id);
        if(strlen($data) > 0){
            $data->delete();
            return response()->json(['message' => 'Deleted Successfully']);
        }
        else{
            return response()->json(['message' => "Don't have comment with id $id."]);
        }
    }

    // Get all activity's comments
    public function getActivityComments($id){
        return CommentCollection::collection(Reaction::where('activity_id',$id)->orderBy('id', 'DESC')->get());
    }

    //Create comment for activity
    public function createActivityComment(Request $request)
    {
        Reaction::updateOrCreate(['user_id' => auth('api')->user()->id, 'activity_id' => $request->id, 'comment' => $request->comment]);
        return response()->json(['message' => 'Commented Successfully']);
    }
}
