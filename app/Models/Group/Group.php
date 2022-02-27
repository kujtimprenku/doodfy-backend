<?php

namespace App;

use App\Traits\Membershipable;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use Membershipable;

    protected $fillable = [
        'user_id',
        'city_id',
        'category_id',
        'access_modifier_id',
        'name',
        'description',
        'group_type',
        'cover_image',
        'facebook_url',
        'instagram_url',
        'twitter_url',
    ];


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

    // public function usersHasInvited($id){

    //     $data = $this->hasMany('App\Inviters')->where('status_id', 0)->where('where_id', $id)->get();
    //     $array = [];

    //     foreach($data as $da){

    //     }
    // }
}
