<?php

namespace App\Http\Controllers;

use App\Activity;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Category;
use App\Http\Resources\ActivityResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserCollection;
use App\UserCategory;
use App\Http\Resources\UserEdit;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $users;
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
    public function show($id){
        return new UserCollection(User::findOrFail($id));
    }

    public function userName(User $user){
        return new UserCollection(User::find($user->id));
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
        $user = User::findOrFail($id);
        $img_url = '';

        if(strpos($request->profile_image, 'base64')){
            $img_url = $user->imageUpload($request->profile_image, 'user', 300, 56);
        }
        else{
            $img_url = $user->profile_image;
        }

        $user->firstname = $request->firstName;
        $user->lastname = $request->lastName;
        $user->email = $request->email;
        $user->city_id = $request->city_id;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->profile_image = $img_url;
        $user->biography = $request->biography;
        $user->facebook_url = $request->facebook_url;
        $user->instagram_url = $request->instagram_url;
        $user->twitter_url = $request->twitter_url;
        $user->birth_date = date("Y-m-d h:i:s", strtotime($request->birth_date));
        $user->update();
        return response()->json(['message' => 'Updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->userCategories()->delete();
        $user->delete();
        return response()->json(["message" => "Successfully removed"]);
    }

    //List of my interests
    public function userInterests(){
        $data =  UserCategory::where('user_id',auth('api')->user()->id)->get();
        $uc = [];
        foreach($data as $da){
            array_push($uc, Category::where('id', $da->category_id)->first());
        }
        return $uc;
    }

    //Show activities created by the users
    public function userActivities($id)
    {
        return ActivityResource::collection(
            Activity::where('user_id', $id)
            ->groupByParentId()
            ->where('inside_id', 1)
            ->orderByDesc('start_date')
            ->get());

    }

    //List of other interests
    public function availableCategory(){
        $my_interests = $this->userInterests();
        $array = [];
        foreach($my_interests as $my_interest){
            array_push($array, $my_interest->id);
        }
        return Category::whereNotIn('id', $array)->get();
    }

    public function getUserEdit(){
        $user = auth('api')->user()->id;
        return new UserEdit(User::find($user));
    }

    public function userReceiptDates(){
        $user = User::find(auth('api')->user()->id);
        $date = date('d/m/y', strtotime($user->created_at));
        return [
            'date' => $date,
            'reference' => 'User registration'
        ];
    }

    public function myXpPurchase(){
        $user =  User::findOrFail(auth('api')->user()->id);
        return $user->myXpPurchase($user->id);
    }

}
