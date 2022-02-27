<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use App\City;
use App\Group;
use App\Company;
use App\ActivityUser;
use App\Traits\Imageable;
use App\Traits\Inviteable;
use App\Traits\Membershipable;
use App\UserXpPoint;
use App\Invites;


class User extends Authenticatable
{
    use Notifiable,
        Friendable,
        Imageable,
        Membershipable,
        Inviteable,
        HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id',
        'role_id',
        'firstname',
        'lastname',
        'username',
        'password',
        'email',
        'gender',
        // 'position',
        'address',
        'phone',
        'mobile',
        'birth_date',
        'profile_image',
        'is_active',
        'xp_points',
        'website',
        'verifyToken',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function getRouteKeyName(){
        return 'username';
    }


    /**
     *
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     *
     */
    public function categories(){
        return $this->belongsToMany('App\Category');
    }
    public function activities(){
        return $this->hasMany('App\Activity');
    }
    public function joins(){
        return $this->belongsToMany('App\Activity');
    }
    public function role(){
        return $this->belongsTo('App\Role');
    }
    public function company(){
        return $this->hasOne('App\Company');
    }
    public function companiesFollow(){
        return $this->belongsToMany('App\Company','company_followers');
    }
    public function my_country_id($city_id){
        $my_city = City::where('id', $city_id)->first();
        return $my_city->country_id;
    }
    public function verifyUser()
    {
        return $this->hasOne('App\VerifyUser');
    }
    public function userCategories(){
        return $this->hasMany('App\UserCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function memberships()
    {
        return $this->morphMany('App\Membership', 'sender');
    }

    public function inviteShips()
    {
        return $this->morphMany('App\Invites', 'sender');
    }

    /**
     *  Scope
     */

    public static function scopeSearch($query, $words){
        $words =  explode(' ', $words);
        return $query->select('id', 'username', 'firstname' , 'lastname', 'email')
            ->where(function($q) use ($words){
                foreach ($words  as $word) {
                    $q->orWhere('username', 'like', "%{$word}%");
                    //   ->orWhere('firstname', 'like', "%{$word}%")
                    //   ->orWhere('lastname', 'like', "%{$word}%");
                }
            });
    }


    public function friendship($friend_id){
        if (auth('api')->check()) {
            $user = auth('api')->user();
            $friend = User::find($friend_id);
            if ($user->hasSentFriendRequestTo($friend)) {
                return 0;
            }
            if ($user->isFriendWith($friend)) {
                return 1;
            }
            if ($user->hasFriendRequestFrom($friend)) {
                return 2;
            }
            if ($user->id == $friend->id) {
                return 3;
            } else {
                return -1;
            }
        } else {
            return -1;
        }
    }

    public function membership($user_id, $group_id)
    {
        $user = User::find($user_id);
        $group = Group::find($group_id);
        if ($group->hasMemberRequestFrom($user))
        {
            return 0;
        }
        else if($group->isMember($user))
        {
            return 1;
        }
        else {
            return -1;
        }
    }


    public function isInvited($user_id, $group_id)
    {
        return Invites::where('recipient_id', $user_id)
            ->where('where_id', $group_id)
            ->where('status_id', 0)
            ->exists();

    }

    public function getAverageRatingsForUser($id){
        $user_activities = Activity::where('user_id',$id)->get();
        $array = [];
        foreach($user_activities as $ua){
            array_push($array, $ua->id);
        }
        $rating = Rating::whereIn('activity_id', $array)->get();
        if($rating->sum('rating') > 0){
            return  round($rating->sum('rating')/count($rating),1);
        }
        else {
            return 0;
        }
    }

    // Get topic category for current user
    public function topicCategories($user_id){
        $activity_ids = ActivityUser::where('user_id', $user_id)->where('has_joined', 1)->groupBy('activity_id')->pluck('activity_id');
        $subcategory_ids = Activity::whereIn('id', $activity_ids)->pluck('subcategory_id');
        $category =  Subcategory::whereIn('id', $subcategory_ids)->get()->map(function($item){
             return $item->categories->pluck('name')->toArray();
         });

        $grouped = collect($category)->groupBy(function ($item) {
            return $item;
        });

        $groupCount = $grouped->map(function ($item) {
            return collect($item)->count();
        });

        $sortByDesc = collect($groupCount)->sort()->reverse()->take(3)->keys();

        $array2 = [];
        for($i = 0; $i<count($sortByDesc); $i++){
            $array2['topic_'.($i+1)] = $sortByDesc[$i];
        }
        return $array2;
    }


    public function countMyXp($user_id){
        return UserXpPoint::where('recipient_id', $user_id)->count();
    }

    public function myXpPurchase($user_id){
        return User::where('id', $user_id)->first()->xp_points;
    }

    //Method call thumbnails images
    public function thumbnail($image, $size){
        if(strpos($image, 'user') !== false){
            return str_replace(['.png', '.jpg'], '_' . $size . 'x' . $size . '.png', $image);
        }
        else {
            return $image;
        }

    }


}
