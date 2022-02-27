<?php

namespace App\Http\Controllers;

use App\Category;
use App\Country;
use Illuminate\Http\Request;
use App\City;
use App\Subcategory;
use App\Http\Resources\Category as CategoryResource;
use App\UserCategory;
use App\Http\Resources\ActivityResource;
use App\Activity;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories = Category::select('id','name', 'image')->orderBy('name','ASC')->get();



        return $categories;
    }


    //Subcategory part
    public function subcategories()
    {
        return $subcategories = Subcategory::select('id','name')->orderBy('name','ASC')->get();
    }

    public function subcategory(Request $request){
            $id = Subcategory::insertGetId([
                'category_id' => 34,
                'name' => ucwords(strtolower($request->name)),
                'created_at' => now()
            ]);
        DB::table('category_subcategory')->insert(['category_id' => 34, 'subcategory_id' => $id]);
        return response()->json(['id'=> $id]);
    }

    //================
    public function initialInfo(){
        $countries=Country::select('id','name')->orderBy('name', 'ASC')->get();
        $cities=City::select('id','country_id','name')->orderBy('name', 'ASC')->get();
        $categories = Category::select('id','name', 'image')->get();
        // foreach($categories as $category){
        //     $img_100x100 = str_replace('.jpg', '_100x100.jpg', $category['image']);
        //     $category['image'] = $img_100x100;
        // }
        return ['countries'=>$countries,'cities'=>$cities, 'categories'=>$categories];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function categoriesAndSubcategories(){
        $categories = Category::select('id','name')->orderBy('name', 'ASC')->get()->toArray();
        $subcategories = Subcategory::select('id','category_id','name')->orderBy('name', 'ASC')->get()->toArray();
        $together = array_merge($categories, $subcategories);
        return collect($together)->sortBy('name')->values();
    }

    public function categoryDetail($id){

        return new CategoryResource(Category::find($id));
    }

    public function subcategoriesByCategoryId($id){
        return Subcategory::where('category_id', $id)->get();
    }
    public function addCategoryToInterests($id){
        UserCategory::updateOrCreate(['category_id'=> $id, 'user_id' => auth('api')->user()->id]);
        return response()->json(['message'=>'Added to favorites']);
    }

    public function removeCategoryFromInterests($id){
       $favoriteCategory = UserCategory::where('category_id',$id)->where('user_id',auth('api')->user()->id);
       $favoriteCategory->delete();
       return response()->json(['message'=>' Deleted from favorites']);
    }


    public function getActivitiesOfCategoryFollowing(){
        $category_following = UserCategory::where('user_id', auth('api')->user()->id)->inRandomOrder()->take(2)->get();
        $array = [];
        foreach($category_following as $cf){
            $categories = Category::where('id', $cf->category_id)->select('id', 'name', 'image')->first();
            $subcategory_id = Subcategory::where('category_id', $categories->id)->get();
            $activities = ActivityResource::collection(Activity::where('subcategory_id', [$subcategory_id[0]->id, [$subcategory_id[1]->id]])->limit(5)->orderByDesc('created_at')->get());
            if(count($activities) > 0){
                $categories->setAttribute('has_favorite', App('App\Category')->hasUserFavorited($categories->id));
                $categories->setAttribute('activities', $activities);
                array_push($array, $categories);
            }
        }
        return  $array;
    }

}
