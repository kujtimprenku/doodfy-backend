<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\UserCategory;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    //
    use SoftDeletes;

    protected $dates=['deleted_at'];

    protected $fillable=[
        'name',
        'image_url'
    ];
    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function subcategories(){
        return $this->belongsToMany('App\Subcategory')->withTimestamps();
    }

    public function categoryFollowers(){
        return $this->belongsToMany('App\User','user_categories');
    }

    public function hasUserSubscribedCategory($category_id){
        $data = DB::table('user_categories')->where('category_id',$category_id)->where('user_id',auth('api')->user()->id)->get();
        if($data->count()>0){
            return true;
        }
        else{
            return false;
        }
    }

    public function hasUserFavorited($category_id){
        if (auth('api')->check()) {
            $data = UserCategory::where('category_id', $category_id)->where('user_id', auth('api')->user()->id)->get();
            return ($data->count() > 0) ? true : false;
        }
        else {
            return false;
        }

    }

}
