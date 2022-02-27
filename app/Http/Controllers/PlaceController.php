<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Http\Resources\ActivityCollection;
use App\Http\Resources\ActivityResource;
use App\Place;
use App\Http\Resources\PlaceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Validator;

class PlaceController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make( $request->all(), [
            // 'title' => 'required',
            // 'description' => 'required',
            // 'location' => 'required',
        ]);
        if ( $validator->fails()) {
            return response()->json( $validator->errors(), 422);
        }
        $place = Place::create([

            'user_id'=> auth()->user()->id,
            'title'=>$request->title,
            'description'=>$request->description,
            'location'=>$request->location,
            'lat'=>$request->lat,
            'lon'=>$request->lon,
            'email'=>$request->email, 
            'phone_number'=>$request->phone_number,
            'address'=>$request->address,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,

        ]);

        if(strpos($request->image, 'base64')){
            $img_url = $place->imageUpload($request->image, 'places', 300, 56);
            $place->image = $img_url;
            $place->update();
        }
        else {
            $img_url = $request->image;
            $place->image = $img_url;
            $place->update();
        }

        return response()->json([
            'message' => 'Successfully created',
            'id' => $place->id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new PlaceResource(Place::find($id));
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
    public function getPlaces(){
      return $places = Place::limit(4)->get();
    }

    public function placeActivities($place_id){
      $activity_id = DB::table('activities')
                    ->where('place_id',$place_id)->get()
                    ->pluck('id');
      return ActivityResource::collection(Activity::find($activity_id));
     }
}
