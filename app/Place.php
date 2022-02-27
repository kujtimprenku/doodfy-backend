<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [

        'user_id',
        'name',
        'description',
        'image',
        'location',
        'address',
        'start_time',
        'end_time'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function users(){
        return $this->belongsToMany('App\Place');
    }

    public function activities(){

        return $this->hasMany('App\Activity');
    }

}
