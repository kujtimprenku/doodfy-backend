<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Company;
use App\Club;
use App\Subcategory;
use App\Category;
use App\UserXpPoint;
use App\ActivityUser;
use App\Http\Resources\Category as CategoryResource;
use Hootlex\Friendships\Traits\Friendable;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\FriendsCollection;
use App\Traits\Imageable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Activity extends Model
{

    use Friendable;
    use Imageable;

    protected $fillable = [
        'user_id',
        'subcategory_id',
        'city_id',
        'place_id',
        'parent_id',
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'min_persons',
        'max_persons',
        'location',
        'has_xp',
        'is_occurrence',
        'lat',
        'lon',
    ];

    /**
     * Relation methods
     */
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function users(){
        return $this->belongsToMany('App\Activity');
    }
    public function userActivity(){
        return $this->hasMany('App\ActivityUser');
    }
    public function hasJoined(){
        return $this->hasMany('App\ActivityUser');
    }
    public function city(){
        return $this->belongsTo('App\City');
    }
    public function place(){
        return $this->belongsTo('App\Place');
    }

    /**
     * Scope
     */
    public function scopeGroupByParentId($query){
        return $query->groupBy(DB::raw('ifnull(parent_id, id)'))
            ->where('start_date', '>', now());
    }

    /**
     *
     */
    public function nrOccurrences($parent_id){
        return $this->whereNotNull('parent_id')->where('parent_id', $parent_id)->get()->count();
    }

    public function usersHasJoined($id){
        $data = $this->hasMany('App\ActivityUser')->where('has_joined', 1)->orderBy('id', 'DESC')->where('activity_id', $id)->get();
        $array = [];
        foreach($data as $da){
            $given_xp = UserXpPoint::where('recipient_id', $da->user_id)->where('activity_id', $id)->count() > 0 ? true : false;
            if(User::find($da->user_id) != null){
                $user = User::find($da->user_id)->only('id', 'username', 'profile_image');
                $user['given_xp'] = $given_xp;
                array_push($array, $user);
            }
        }
        return $array;
    }
    public function friendsHasJoined($id){
        $data = $this->hasMany('App\ActivityUser')->where('has_joined', 1)->orderBy('id', 'DESC')->where('activity_id', $id)->get();
        $array = [];
        $array_friends=[];
        foreach($data as $da){
            $friend = User::find($da->user_id);
            if(auth('api')->user()->isFriendWith($friend)){
                array_push($array_friends, User::find($da->user_id)->only(['id','username','profile_image']));
            }
            else {
                array_push($array, User::find($da->user_id)->only(['id','username','profile_image']));
            }
        }
        $fiveFriends = array_slice($array_friends, 0, 5);
        $plusJoined = count($array_friends) - count($fiveFriends);
        $others = count($array) + $plusJoined;
        return ['friends' => $fiveFriends, 'nr_joins' => $others];
    }
    public function inviteUnJoinedFriends($id){
        $data = ActivityUser::where('has_joined', 1)->orderBy('id', 'DESC')->where('activity_id', $id)->get();
        $array_friends = [];
        $my_friends = FriendsCollection::collection(auth('api')->user()->getFriends());
        foreach($data as $da){
            $friend = User::find($da->user_id);
            if(auth('api')->user()->isFriendWith($friend)){
                array_push($array_friends, User::find($da->user_id)->only(['id','username','profile_image']));
            }
        }
        $friends_joined_ids = collect($array_friends)->pluck('id')->toArray();
        foreach($my_friends as $friend){
            if(in_array($friend->id, $friends_joined_ids)){
                $friend['has_joined'] = true;
            }
            else{
                $friend['has_joined'] = false;
            }
        }
        return $my_friends;

    }
    // public function comments($id){
    //     $data = $this->hasMany('App\Reaction')->where('activity_id', $id)->get();
    //     $array = [];
    //     foreach($data as $da){
    //         array_push($array, User::find($da->user_id)->select(['id','username','profile_image'])->first()->setAttribute('comment',$da->comment));
    //     }
    //     return $array;
    // }
    public function comments($id){
        return CommentCollection::collection(Reaction::where('activity_id',$id)->orderBy('id', 'DESC')->get());
    }

    public function categoryFromSubcategory($subcategory_id){
        $subcategory = Subcategory::find($subcategory_id);
        $categories = $subcategory->categories;
        return CategoryResource::collection($categories);
    }


    public function rating(){
        return $this->hasMany('App\Rating');
    }

    public function hasRated(){
        return $this->hasMany('App\ActivityUser');
    }
    public function findNrRaters($id){
        $rating  = Rating::where('activity_id',$id)->get();
        $rating_obj = collect();
        if( $rating->sum('rating')>0){
            $rating_obj->push(['avg' => round($rating->sum('rating')/count($rating),1), 'nr_raters' => count($rating)]);
        }
        else{
           return 0;
        }
        return $rating_obj[0];
    }

    public function usernameByRole($user_id, $role_id){
        if($role_id == 1) {
            $user =  User::find($user_id)->only('id', 'username');
            $user['role'] = 'user';
            return $user;
        }
        else if($role_id == 2) {
            $company = Company::where('user_id', $user_id)->select('id', 'firm')->first();
            $company['role'] = 'company';
            return $company;
        }
        else{
            return 'No name';
        }
    }

    //Method call thumbnails images
    public function thumbnail($image, $size){
        if(strpos($image, 'activity') !== false){
            return str_replace(['.jpg', '.jpeg'], '_' . $size . 'x' . $size . '.jpg', $image);
        }
        else {
            return $image;
        }

    }

}
