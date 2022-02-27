<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    //
    // use SoftDeletes;

    // protected $dates=['deleted_at'];

    protected $fillable = [
        'name',
        'post_code',
        'lat',
        'lon'
    ];


    public function country(){
        return $this->belongsTo('App\Country');
    }
}
