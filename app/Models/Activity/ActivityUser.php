<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityUser extends Model
{
    protected $fillable=[
        'activity_id',
        'user_id',
        'has_joined',
        'has_saved',
        'xp_points'
    ];

    public function activities(){
        return $this->belongsToMany('App\Activity', 'activity_users', 'id');
    }
    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function scopeHasJoined($query){
        return $query->where('has_joined', 1);
    }
    public function scopeHasSaved($query){
        return $query->where('has_saved', 1);
    }
    public function scopeUserId($query, $user_id){
        return $query->where('user_id', $user_id);
    }

}
