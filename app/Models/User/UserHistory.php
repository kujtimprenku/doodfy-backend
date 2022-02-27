<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserHistory extends Model{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'activity_id',
    ];

    public function activities(){
        return $this->belongsToMany('App\Activity', 'user_histories', 'id');
    }


}
