<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{

    protected $fillable = [
        'user_id',
        'activity_id',
        'comment'
    ];


    protected $table = 'activity_reaction';


    public function activity(){
        return $this->belongsTo('App\Activity');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

}
