<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
     protected $fillable=[
        'activity_id',
        'user_id',
        'rating'

     ];

     public function user(){
        return $this->belongsTo('App\User');
    }




}
