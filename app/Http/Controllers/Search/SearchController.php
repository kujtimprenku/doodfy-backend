<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ActivityResource;
use App\Activity;
use App\Company;
use App\Category;
use App\Subcategory;
use Illuminate\Support\Carbon;
use App\Http\Resources\Company as CompanyResource;
use App\City;
use App\User;
use App\Http\Resources\ActivityCollection;

class SearchController extends Controller
{
    /**
     * User search
     */
    public function getUserSearch(Request $request)
    {
        $words = $request->input('search');

        $users = User::select('id', 'username', 'email', 'profile_image')->where('username','LIKE',$words.'%')->get();

        if (auth('api')->check()) {
            DB::table('search_history')->insert(['user_id' => auth('api')->user()->id, 'word' => $words, 'created_at' => now()]);

            $array=[];
            foreach ($users as $user) {
                array_push($array, $user->setAttribute('friendship', app('App\User')->friendship($user->id)));
            }
        }
        $activities = Activity::where('title','LIKE', '%'.$words.'%')->paginate();
        $companies = CompanyResource::collection(Company::where('firm','LIKE', '%'.$words.'%')->get());
        return [
            "users"=>$users,
            "activities"=>new ActivityCollection($activities),
            "companies"=>$companies,
        ];
    }
    public function autocomplete(Request $request){
        $words =  $request->input('search');
        $users = User::search($words)->limit(3)->get();
        $companies = Company::select('id', 'firm')->where('firm', 'LIKE', '%'.$words.'%')->limit(3)->get();
        $categories = Category::select('id', 'name')->where('name', 'LIKE', '%'.$words.'%')->limit(3)->get();
        $subcategories = Subcategory::select('id', 'name')->where('name', 'LIKE', '%'.$words.'%')->limit(3)->get();

        return [
            "users"=>$users,
            "companies"=>$companies,
            "categories"=>$categories,
            "subcategories"=>$subcategories,
        ];
    }
    public function getSwitzerlandCities(){
        return City::where('country_id', 18)->orderBy('name')->get();
    }

      //Filter Activity by City
    public function getActivitiesByCity($id)
    {
        $activities =  ActivityResource::collection(Activity::where('city_id', $id)->where('start_date', '>', now())->orderBy('created_at', 'DESC')->limit(12)->get());
        return [
            "users"=>[],
            "activities"=>$activities,
            "companies"=> [],
        ];
    }
    //Filter Activity by City this Week
    public function getActivitiesByCityThisWeek($id){
         $now =Carbon::now()->format('Y-m-d');
         $addWeek = Carbon::now()->addWeek(1)->format('Y-m-d');
        return ActivityResource::collection(Activity::where('city_id',$id)->whereBetween('start_date',[$now,$addWeek])->get());

    }

    //Filter Activity by City This Month
    public function getActivitiesByCityThisMonth($id){
        $now = Carbon::now();
        $addMonth = Carbon::now()->addMonth(1)->format('Y-m-d');
        return ActivityResource::collection(Activity::where('city_id',$id)->whereBetween('start_date',[$now,$addMonth])->get());
    }

    // Filter activity by Category
    public function getActivitiesByCategory($id){
      $array=[];
      $subcategory = Subcategory::where('category_id',$id)->get();
      foreach($subcategory as $sa){
          array_push($array,$sa->id);
      }
       return ActivityResource::collection(Activity::whereIn('subcategory_id',$array)->get());
    }

    // Filter activity by Date
    public function getActivitiesByDate($date){
        return ActivityResource::collection(Activity::where('start_date','LIKE','%'.$date.'%')->get());
    }

    /**
     * EXPLORE - Search filters
     */
    public function searchFilters(Request $request, Activity $activity){
        if (($request->city != null) || ($request->category != null) || ($request->from != null) || ($request->to != null)) {
            $activity = $activity->newQuery();
            // Search with city.
            if ($request->has('city')  && $request->city != null) {
                $city = City::where('name', $request->city)->first();
                $activity->where('city_id', $city->id)
                    // ->where('end_date', '>', now())
                    ->orderBy('end_date', 'ASC')
                    ->get();
            }
            //Search with category
            if ($request->has('category') && $request->cat == 'true' && $request->category != null) {
                $category = Category::where('name', $request->category)->first();
                $subcategory_ids = $category->subcategories->pluck('id');
                $activity->whereIn('subcategory_id', $subcategory_ids)
                    // ->where('end_date', '>', now())
                    ->orderBy('end_date', 'ASC')
                    ->get();
            }
            //Search with subcategory
            if ($request->has('category') && $request->cat == 'false' && $request->category != null) {
                $subcategory_id = Subcategory::where('name', $request->category)->first()->id;
                $activity->where('subcategory_id', $subcategory_id)
                    // ->where('end_date', '>', now())
                    ->orderBy('end_date', 'ASC')
                    ->get();
            }
            //Search with date - From
            if ($request->has('from') && $request->from != null) {
                $activity->where('start_date', '>', date('Y-m-d H:i:s', strtotime($request->from)))
                    ->orderBy('end_date', 'ASC')
                    ->get();
            }
             //Search with date - To
             if ($request->has('to') && $request->to != null) {
                $activity->where('start_date', '<', date('Y-m-d H:i:s', strtotime($request->to)))
                    ->orderBy('end_date', 'ASC')
                    ->get();
            }
            return [
                'users' => [],
                'activities' => new ActivityCollection($activity->paginate(15)),
                "companies"=> []
            ];
        }
        else {
            return app('App\Http\Controllers\ActivityController')->activities();
        }
    }


}
