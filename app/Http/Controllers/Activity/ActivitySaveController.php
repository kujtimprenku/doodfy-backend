<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityUser;
use App\Http\Resources\ActivityBookedAndSaved;
use Illuminate\Http\Request;

class ActivitySaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $saved_activity = ActivityUser::with('activities')
            ->hasSaved()
            ->userId(auth('api')->user()->id)
            ->latest()
            ->get()
            ->pluck('activities')
            ->flatten();

        return ActivityBookedAndSaved::collection($saved_activity);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ActivityUser::updateOrCreate(['user_id' => auth('api')->user()->id, 'activity_id' =>  $request->id, 'has_saved' => 1]);

        return response()->json(['message' => 'Saved successfully']);
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
        $activity = ActivityUser::where('activity_id',  $id)
            ->userId(auth('api')->user()->id)
            ->hasSaved();

        $activity->delete();

        return response()->json(['message' => 'Unsaved successfully']);
    }

}
