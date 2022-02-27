<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserHistory;
use App\Http\Resources\ActivityResource;
use App\Activity;
use Illuminate\Support\Facades\Cache;

class UserHistoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_histories = UserHistory::with('activities')
            ->where('user_id', auth('api')->user()->id)
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->pluck('activities')
            ->collapse();

        return ActivityResource::collection($user_histories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_histories = UserHistory::updateOrCreate([
            'user_id' => auth('api')->user()->id,
            'activity_id' => $request->activity_id,
        ]);
        $user_histories->increment('times');
        $user_histories->touch();
        return [];
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
    public function destroy($id){
    //    UserHistory::where('user_id', auth('api')->user()->id)->where('activity_id', $id)->delete();
    //    Cache::forget('userHistories');
    //    return response()->json(['message' => 'Removed successfully']);
    }

    public function destoryHistories(){
        UserHistory::where('user_id', auth('api')->user()->id)->delete();
        Cache::forget('userHistories');
        return response()->json(['message' => 'Removed successfully']);
    }

}
