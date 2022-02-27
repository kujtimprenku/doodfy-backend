<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    //
    use SoftDeletes;

    protected $dates=['deleted_at'];

    protected $fillable=[
        'name',
        'image_url'
    ];

    public function categories(){
        return $this->belongsToMany('App\Category')->withTimestamps();
    }
}
