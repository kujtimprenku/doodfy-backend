<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Activity;
use App\User;
use App\ActivityUser;
use App\Http\Resources\Activity as ActivityDetailResource;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ActivityBookedAndSaved;
use Illuminate\Support\Facades\Validator;
use App\Reaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use App\Company;
use Illuminate\Support\Carbon;
use App\City;
use App\UserCategory;
use App\Category;
use App\Group;
use App\Subcategory;
use App\Http\Resources\Company as CompanyResource;
use App\Http\Resources\ActivityEdit;
use App\Notifications\InviteFriendsToActivity;
use App\Notifications\JoinMyActivity;
use App\UserHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ActivityCollection;
use DateTime;
use When\When;

class ActivityController extends Controller
{
    public  $base;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {
         $activity_id = '';
        //  $validator = Validator::make( $request->all(), [
        //     'subcategory_id' => 'required',
        //     'title' => 'required',
        //     'description' => 'required',
        //     'start_date' => 'required|date|after:now',
        //     'end_date' => 'required|date|after:start_date',
        //     'location' => 'required',
        // ]);
        // if ( $validator->fails()) {
        //     return response()->json( $validator->errors(), 422);
        // }

        //Activity repeat
        $occurrences = $request->occurrences;

        if($request->occurrences['is_repeat'] == 'true')
        {
            $activity_id = '';
            $r = new When();
            if ($occurrences['count'] == 0 && $occurrences['frequency'] != 'daily') {
                $r->startDate(new DateTime($request->start_date))
                    ->freq($occurrences['frequency'])
                    ->interval($occurrences['interval'])
                    ->byday($occurrences['byday'])
                    ->until(new DateTime($request->end_date))
                    ->generateOccurrences();
            }
            if($occurrences['count'] > 0 && $occurrences['frequency'] != 'daily'){
                $r->startDate(new DateTime($request->start_date))
                    ->freq($occurrences['frequency'])
                    ->interval($occurrences['interval'])
                    ->byday($occurrences['byday'])
                    ->count($occurrences['count'])
                    ->generateOccurrences();
            }
            if ($occurrences['count'] == 0 && $occurrences['frequency'] == 'daily') {
                $r->startDate(new DateTime($request->start_date))
                    ->freq($occurrences['frequency'])
                    ->interval($occurrences['interval'])
                    ->until(new DateTime($request->end_date))
                    ->generateOccurrences();
            }
            if($occurrences['count'] > 0 && $occurrences['frequency'] == 'daily'){
                $r->startDate(new DateTime($request->start_date))
                    ->freq($occurrences['frequency'])
                    ->interval($occurrences['interval'])
                    ->count($occurrences['count'])
                    ->generateOccurrences();
            }

            $activity_parent = DB::table('activity_repeat')->insertGetId([
                    'user_id' => auth('api')->user()->id,
                    'subcategory_id' =>  $request->subcategory_id,
                    'city_id' =>  auth('api')->user()->city_id,
                    'title' =>  $request->title,
                    'description' =>  $request->description,
                    'start_date' => date("Y-m-d H:i:s", strtotime($request->start_date)),
                    'end_date' => date("Y-m-d H:i:s", strtotime($request->end_date)),
                    'min_persons' =>  $request->min_persons,
                    'max_persons' =>  $request->max_persons,
                    'location' =>  $request->location,
                    'has_xp' =>  $request->has_xp,
                    'occurrence' =>  collect($request->occurrences)->toJson(),
                    'lat' =>  $request->lat,
                    'lon' =>  $request->lon,

            ]);

            collect($r->occurrences)->each(function($item) use($request, $activity_parent){
                $activity = Activity::create([
                    'user_id' => auth('api')->user()->id,
                    'subcategory_id' =>  $request->subcategory_id,
                    'city_id' =>  auth('api')->user()->city_id,
                    'parent_id' =>  $activity_parent,
                    'place_id'=> $request->place_id,
                    'title' =>  $request->title,
                    'description' =>  $request->description,
                    'start_date' => $item,
                    'end_date' => $item->format('Y-m-d ') . date_format(new DateTime($request->end_date), 'H:i:s'),
                    'min_persons' =>  $request->min_persons,
                    'max_persons' =>  $request->max_persons,
                    'location' =>  $request->location,
                    'has_xp' =>  $request->has_xp,
                    'is_occurrence' =>  1,
                    'lat' =>  $request->lat,
                    'lon' =>  $request->lon,
                ]);

                if(strpos($request->image, 'base64')){
                    $img_url = $activity->imageUpload($request->image, 'activity', 300, 56);
                    $activity->image = $img_url;
                }
                else {
                    $activity->image = $request->image;
                }
                if ($request->group_id != null) {
                    $activity->inside_id = 2;

                    DB::table('group_activity')->insert([
                        'group_id' => $request->group_id,
                        'activity_id' => $activity->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                $activity->update();


            });


        }
        else {
            $activity = Activity::create([
                'user_id' => auth('api')->user()->id,
                'subcategory_id' =>  $request->subcategory_id,
                'city_id' =>  auth('api')->user()->city_id,
                'place_id'=> $request->place_id,
                'title' =>  $request->title,
                'description' =>  $request->description,
                'start_date' => date("Y-m-d H:i:s", strtotime($request->start_date)),
                'end_date' => date("Y-m-d H:i:s", strtotime($request->end_date)),
                'min_persons' =>  $request->min_persons,
                'max_persons' =>  $request->max_persons,
                'location' =>  $request->location,
                'has_xp' =>  $request->has_xp,
                'lat' =>  $request->lat,
                'lon' =>  $request->lon,
            ]);

            if (strpos($request->image, 'base64')) {
                $img_url = $activity->imageUpload($request->image, 'activity', 300, 56);
                $activity->image = $img_url;
            } else {
                $activity->image = $request->image;
            }

            if ($request->group_id != null) {
                $activity->inside_id = 2;

                DB::table('group_activity')->insert([
                    'group_id' => $request->group_id,
                    'activity_id' => $activity->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $group = Group::find($request->group_id);
                $result = $group->getAcceptedMemberships();
                $users_id = $result->pluck('sender_id');
                $users = User::whereIn('id', $users_id)->get();
                foreach($users as $user){
                    $user->notify(new InviteFriendsToActivity($activity));
                }
            }

            $activity->update();
            $activity_id = $activity->id;
        }

        return response()->json([
            'message' => 'Successfully created',
            'id' => $activity_id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int   $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id)
    {
        return new ActivityDetailResource(Activity::find( $id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  int   $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request,  $id)
    {
        // $validator = Validator::make( $request->all(), [
        //     'subcategory_id' => 'required',
        //     'title' => 'required',
        //     'description' => 'required',
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date',
        //     'location' => 'required',
        // ]);
        // if ( $validator->fails()) {
        //     return response()->json( $validator->errors(), 422);
        // }
        if ($request->edit_all)
        {
            $parent_id = Activity::where('id', $id)->first()->parent_id;
            $activities = Activity::where('parent_id', $parent_id)->get();
            foreach ($activities as $activity){
                $this->updateActivity($request, $activity, $all = true);
            }
            return response()->json(['message' => 'Updated Successfully']);
        }
        else {
            $activity = Activity::findOrFail($id);

            $this->updateActivity($request, $activity);

            return response()->json(['message' => 'Updated Successfully']);
        }


    }

    public function updateActivity($request, $activity, $all = false)
    {
            if (strpos($request->image, 'base64'))
            {
                $img_url = $activity->imageUpload($request->image, 'activity', 300, 56);
            } else {
                $img_url = $request->image;
            }

            $activity->subcategory_id = $request->subcategory_id;
            $activity->city_id = auth('api')->user()->city_id;
            $activity->title = $request->title;
            $activity->description = $request->description;
            $activity->image = $img_url;
            if(!$all){
                $activity->start_date = date("Y-m-d H:i:s", strtotime($request->start_date));
                $activity->end_date = date("Y-m-d H:i:s", strtotime($request->end_date));
            }
            $activity->min_persons = $request->min_persons;
            $activity->max_persons = $request->max_persons;
            $activity->location = $request->location;
            $activity->has_xp = $request->has_xp;
            $activity->lat = $request->lat;
            $activity->lon = $request->lon;
            $activity->update();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int   $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        if(strlen($activity) > 0){
            $str= explode('/' , $activity->image);
            $img_url = explode('.', end($str));
            $path = '/activity//'.date('Y').'/'.date('m').'/';

            Storage::disk('local')->delete($path . $img_url[0].'.png');
            Storage::disk('local')->delete($path . $img_url[0].'_300x300.png');
            Storage::disk('local')->delete($path . $img_url[0].'_56x56.png');

            $activity->delete();
            return response()->json(['message' => 'Deleted Successfully']);
        }
        else{
            return response()->json(['message' => "Don't have activity with id  $activity->id."]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int   $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAll($parent_id)
    {
        $activities = Activity::where('parent_id', $parent_id)->get();
        foreach($activities as $activity){
            $activity->delete();
        }
        return response()->json(['message' => 'Deleted Successfully']);

    }

    //Replicate activity
    public function replicateActivity($id){
        $activity = Activity::findorfail($id);
        $new_activity = $activity->replicate();
        $new_activity->save();
        return response()->json(['id' => $new_activity->id]);
    }

    public function getActivityEdit($id){
        return new ActivityEdit(Activity::find($id));
    }


    public function activityOccurrences($parent_id){
        return ActivityResource::collection(
            Activity::where('parent_id', $parent_id)->get()
        );
    }
    public function activitiesWithDetail()
    {
        return ActivityResource::collection(Activity::all()->where('city_id', auth('api')->user()->city_id));
        //return ActivityResource::collection(Activity::all());
    }
    public function activities()
    {
        $activities =  new ActivityCollection(
            Activity::where('start_date', '>', now())
                ->groupByParentId()
                ->orderBy('start_date','ASC')
                ->inRandomOrder()
                ->paginate(15));

        return [
            'users' => [],
            'activities' => collect($activities)->sortByDesc('has_xp',true)->toArray(),
            'companies' => [],
        ];
    }
    //Show Activity by City
    public function getActivitiesByCity()
    {
        return ActivityResource::collection(Activity::where('city_id', auth('api')->user()->city_id)->where('start_date', '>', now())->orderBy('created_at', 'DESC')->limit(12)->get());
    }

    //Show Activities by Country And Interests
    public function getActivitiesByCountryAndInterests(){
        $user_city = City::where('id',auth('api')->user()->city_id)->first();
        $country_id = $user_city->country_id;
        $cities = City::where('country_id', $country_id)->get();
        $array = [];
        $array2 = [];
        $category = UserCategory::where('user_id',auth('api')->user()->id)->get();
        foreach($category as $fc){
            $findCategory =$fc->category_id;
            array_push($array2,  $findCategory);
        }
            $subcategories = Subcategory::whereIn('category_id',$array2)->get();
            $array3 =[];
        foreach($subcategories as $sc){
            $SubCategory =$sc->id;
            array_push($array3,  $SubCategory);
        }
        foreach($cities as $city){
                $activityBycity=ActivityResource::collection(Activity::where('city_id', $city->id)->whereIn('subcategory_id',$array3)->where('start_date', '>', now())->orderBy('created_at', 'DESC')->get())->first();
            array_push($array, $activityBycity);
        }
        $arrayOfActivitiesByState = array_values(array_filter(array_splice($array,0,12)));
        return collect($arrayOfActivitiesByState)->sortByDesc('has_xp',true)->values()->toArray();
    }

    /**
     * Show Activities by Country
     */
    public function startRecommendedActivities()
    {
        $cities_id = City::where('id', auth('api')->check() ? auth('api')->user()->city_id : 310)
            ->first()
            ->country
            ->cities
            ->pluck('id');

        $activities_by_country = Activity::whereIn('city_id', $cities_id)
            // ->groupByParentId()
            // ->where('inside_id', 1)
            // ->where('start_date', '>', now())
            // ->where('start_date', '<', Carbon::now()->addMonths(2))
            ->orderBy('start_date')
            ->limit(12)
            ->get();

        return ActivityResource::collection($activities_by_country)
            // ->sortBy('user.role')
            // ->sortByDesc('has_xp', true)
            ->values();
    }

    //Shows the activity of the current user that has join
    public function getBookedActivities()
    {
         $hasjoin = ActivityUser::where('user_id', auth('api')->user()->id)->hasJoined()->get();
         $hj = [];
        foreach ( $hasjoin as  $h) {
            array_push( $hj, ActivityBookedAndSaved::collection(Activity::all()->where('id',  $h->activity_id)->where('end_date', '>', now()))->first());
        }
        return  array_reverse(array_filter($hj));
    }
    //Show history activities of current user
    public function getParticipatedActivities(){
         $userJoined = ActivityUser::userId(auth('api')->user()->id)->hasJoined()->get();
         $uj = [];
        foreach ( $userJoined as  $u) {
            array_push( $uj, ActivityResource::collection(Activity::all()->where('id',  $u->activity_id)->where('end_date', '<', now()))->first());
        }
        return array_reverse(array_values(array_filter($uj)));
    }

    //Show history activities by user id
    public function getParticipatedActivitiesByUser($id)
    {
         $hasjoin = ActivityUser::userId($id)->hasJoined()->get();
         $hj = [];
        foreach ( $hasjoin as  $h) {
            array_push( $hj, ActivityResource::collection(Activity::all()->where('id',  $h->activity_id)->where('end_date', '<', now()))->first());
        }
        return array_reverse(array_values(array_filter($hj)));
    }

    // Join Activity
    public function joinActivity(Request $request)
    {
         $activity = Activity::find($request->id);
         $nr_joined =  $activity->hasJoined->sum('has_joined');

        if($nr_joined < $activity->max_persons || $activity->max_persons == null){     //null is unlimited
            ActivityUser::updateOrCreate(['user_id' => auth('api')->user()->id, 'activity_id' =>  $request->id, 'has_joined' => 1]);
            $user = User::find($activity->user_id);
            $user->notify(new JoinMyActivity($activity));

            return response()->json(['message' => 'Joined successfully']);
        }
        else{
            return response()->json(['message' => 'Sorry, activity has reached the maximum number']);
        }

    }
    // Unjoin Activity
    public function unJoinActivity( $id)
    {
         $activity = ActivityUser::where('activity_id',  $id)
        ->where('user_id', auth('api')->user()->id)
        ->hasJoined();
         $activity->delete();
        return response()->json(['message' => 'Unjoined successfully']);
    }
    //
    public function getActivityComments( $id){
         $data = Reaction::where('activity_id',  $id)->get();
         $array = [];
        foreach( $data as  $da){
            array_push( $array, User::findOrFail( $da->user_id)
            ->select(['id','username','profile_image'])
            ->first()
            ->setAttribute('comment', $da->comment));
        }
        return  $array;
    }

    //Return Latest activities by company
    public function latestActivitiesByCompany( $id){
        $company = Company::find( $id);
        return ActivityResource::collection(Activity::all()->where('user_id', $company->user_id))->sortByDesc('start_date');
    }
    //Return all activities by city this week
        public function getActivitiesByCityThisWeek(){
             $array = [];
             $company = Company::all();
             $now = Carbon::now()->format('Y-m-d');
             $addWeek = Carbon::now()->addWeek(1)->format('Y-m-d');
            foreach( $company as  $co){
                 $activityBycity=  ActivityResource::collection(Activity::where('city_id',auth('api')->user()->city_id)->where('user_id', $co->user_id)->whereBetween('start_date', [ $now,  $addWeek])->get())->first();
                array_push( $array, $activityBycity);
            }
            return array_values(array_filter( $array));
        }

      //Return all activities by city this month
      public function getActivitiesByCityThisMonth(){
         $array = [];
         $company = Company::all();
         $now = Carbon::now()->format('Y-m-d');
         $addWeek = Carbon::now()->addWeek(1)->format('Y-m-d');
         $addMonth = Carbon::now()->addMonth(1)->format('Y-m-d');
        foreach( $company as  $co){
             $activityBycity=  ActivityResource::collection(Activity::where('city_id',auth('api')->user()->city_id)
                ->where('user_id', $co->user_id)
                ->whereBetween('start_date', [ $now,  $addMonth])
                ->whereNotBetween('start_date', [ $now,  $addWeek])
                ->orderBy('start_date','asc')->get())->first();
            array_push( $array, $activityBycity);
        }
        return array_filter( $array);
    }
    //Return companies with their activities created this months
    public function getActivitiesOfCompaniesByCityThisMonth(){
         $array = [];
         $companies = Company::all();
         $now = Carbon::now()->format('Y-m-d');
         $addMonth = Carbon::now()->addMonth(1)->format('Y-m-d');
        foreach( $companies as  $co){
             $activityBycity = ActivityResource::collection(Activity::where('user_id', $co->user_id)->whereBetween('start_date', [ $now,  $addMonth])->orderBy('start_date')->get())->first();
             $co->setAttribute('activities', $activityBycity);
            array_push( $array, $co);
        }
        return array_filter( $array);
    }

    /**
     * Company by country
     */
    public function getCompaniesFromMyCountry(){
        $my_city = City::where('id', auth('api')->check() ? auth('api')->user()->city_id : 310)->first();
        $country_id = $my_city->country_id;
        return CompanyResource::collection(Company::where('country_id', $country_id)->get());
    }

    // Invites all friends to activity
    public function inviteAllFriendsToActivity($activity_id){
        $activity = Activity::find($activity_id);
        $user = auth('api')->user();
        $my_friends = $user->getFriends();
        foreach($my_friends as $my_friend){
            $my_friend->notify(new InviteFriendsToActivity($activity));
        }
        return response()->json(['message' => 'Invited all']);
    }

    // Invite list
    public function inviteUnjoinedFriends($id){
        return app('App\Activity')->inviteUnJoinedFriends($id);
    }

    // Invites specific friend to activity
    public function inviteFriendToActivity(Request $request){
        $sender = auth('api')->user();
        $activity = Activity::find($request->activity_id);
        $recipient = User::find($request->friend_id);
        $sender->sendInvite($recipient, $activity);
        $recipient->notify(new InviteFriendsToActivity($activity));
        return response()->json(['message'=> $recipient->username. ' has been invited to activity.']);
    }

    // Most viewed activities
    public function mostViewedActivities()
    {
        $most_viewed_activities = UserHistory::withTrashed()
            ->groupBy('activity_id')
            ->selectRaw('SUM(times) as times, activity_id')
            ->join('activities', 'activities.id', '=', 'user_histories.activity_id')
            // ->where('activities.end_date', '>', now())
            ->orderByDesc('times')
            ->limit(12)
            ->pluck('activity_id')
            ->toArray();;

        $ordered_ids= implode(",", $most_viewed_activities);

        return ActivityResource::collection(
            Activity::whereIn('id', $most_viewed_activities)
                ->orderByRaw(DB::raw("FIELD(id,  $ordered_ids)"))
                ->get()
            );
    }

    // Most joined activities
    public function mostJoinedActivities()
    {
        $most_joined_activities = ActivityUser::hasJoined()
            ->groupBy('activity_id')
            ->selectRaw('count(*) as total, activity_id')
            ->join('activities', 'activities.id', '=', 'activity_users.activity_id')
            // ->where('activities.end_date', '>', now())
            ->orderByDesc('total')
            ->limit(12)
            ->pluck('activity_id')
            ->toArray();

        $ordered_ids= implode(",", $most_joined_activities);

        return ActivityResource::collection(
            Activity::whereIn('id', $most_joined_activities)
                ->orderByRaw(DB::raw("FIELD(id,  $ordered_ids)"))
                ->get()
            );
    }

    //Photo of category from subcateogry
    public function categoryImageFromSubcategory($id){
        $subcategory = Subcategory::find($id);
        if(empty($subcategory)){
            return [env('APP_PATH_UPLOADS') . 'category/Other.jpg'];
        }
        else{
            return $subcategory->categories->pluck('image');
        }
    }


}
