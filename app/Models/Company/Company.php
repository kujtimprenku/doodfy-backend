<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Activity;
use App\Http\Resources\ActivityCollection;
use App\CompanyFollower;
class Company extends Model
{
    protected $fillable = [
        'user_id',
        'country_id',
        'city_id',
        'firm',
        'street',
        'branch',
        'website',
        'profile_logo',
        'cover_image',
        'logo',
        'facebook_url',
        'instagram_url',
        'twitter_url'
    ];

    public function followers(){
        return $this->belongsToMany('App\User','company_followers');
    }

    public function activities($user_id){
        $activity = $this->hasMany('App\Activity','user_id', 'user_id')->where('user_id', $user_id)->get();
        return ($activity->count() > 0) ? true : false;
    }

    public function hasUserSubscribed($company_id)
    {
        $data = DB::table('company_followers')
            ->where('company_id', $company_id)
            ->where('user_id', auth('api')->check() ? auth('api')->user()->id : null)
            ->exists();

        return $data;
    }

    // public function companyActivities($user_id){
    //     $data = $this->hasMany('App\CompanyFollower')->where('user_id', $user_id)->get();
    //     $array = [];
    //     foreach($data as $da){
    //         array_push($array, ActivityCollection::collection(Activity::all()->where('user_id', $da->user_id))->first());
    //     }
    //     return $array;
    // }
}
