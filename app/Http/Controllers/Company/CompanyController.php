<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Company as CompanyResource;
use App\Company;
use App\Activity;
use App\Http\Resources\ActivityResource;
use App\ActivityUser;
use App\CompanyFollower;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return CompanyResource::collection(Company::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        return new CompanyResource(Company::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $company = Company::findOrFail($id);
        if($request->has('image')) {
            $uri =  $request->image;
            $img = explode(',', $uri);
            $ini =substr($img [0], 11);
            $extension = explode(';', $ini);
            $imageName = str_random(30).'.'. $extension [0];
            Storage::disk('local')->put('company/'. $imageName, fopen($request->image, 'r+'));
            $imageName = 'http://doodfy.ch/uploads/company/' .  $imageName;
            $company->cover_image = $imageName;
            $company->update();
            return response()->json(['message' => 'Updated Successfully']);
        }
        else if(strpos($request->logo, 'base64')){
            $uri = $request->logo;
            $img = explode(',',$uri);
            $ini = substr($img [0], 11);
            $extension = explode(';', $ini);
            $imageName = str_random(30).'.'.$extension [0];
            Storage::disk('local')->put('company/'.$imageName, fopen($request->logo,'r+'));
            $imageName = 'http://doodfy.ch/uploads/company/' . $imageName;
            $company->logo = $imageName;
            $company->update;
            return response()->json(['message' => 'Logo Updated Successfully']);

        }
        else{
            $company->firm = $request->firm;
            $company->street = $request->street;
            $company->branch = $request->branch;
            $company->logo = $request->logo;
            $company->website = $request->website;
            $company->description = $request->description;
            $company->facebook_url = $request->facebook_url;
            $company->instagram_url = $request->instagram_url;
            $company->twitter_url = $request->twitter_url;
            $company->update();
            return response()->json(['message' => 'Updated Successfully']);
        }
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
    public function activitiesByCompanyId($id)
    {
        $company = Company::find($id);
        return  ActivityResource::collection(Activity::all()->where('user_id', $company->user_id));
    }

    public function subscribeCompany($id)
    {
        CompanyFollower::updateOrCreate(['company_id' => $id, 'user_id' => auth('api')->user()->id]);
        return response()->json(['message' => 'Subscription added']);
    }

    public function unsubscribeCompany($id)
    {
        $companySubscribe = CompanyFollower::where('company_id', $id)->where('user_id', auth('api')->user()->id);
        $companySubscribe->delete();
        return response()->json(['message' => 'Successfully removed']);
    }

    public function userCompanies()
    {
        $data =  DB::table('company_followers')->where('user_id', auth('api')->user()->id)->get();
        $uc = [];
        foreach ($data as $da) {
            array_push($uc, Company::where('id', $da->company_id)->select(['id', 'firm','logo'])->first());
        }
        return $uc;
    }

    //Activities of Company
    public function activitiesOfCompany()
    {
        $company_following = CompanyFollower::where('user_id', auth('api')->user()->id)->inRandomOrder()->take(2)->get();
        $array = [];

        foreach ($company_following as $cf) {
            $company = Company::find($cf->company_id);
            $activitiesCompany = ActivityResource::collection(Activity::where('user_id', $company->user_id)->limit(5)->orderBy('created_at')->get());
            if (count($activitiesCompany) > 0) {
                $company->setAttribute('activities', $activitiesCompany);
                array_push($array, $company);
            }
        }
        return $array;
    }

    //Trending Activities

    public function trendingActivities()
    {
        $array = [];
        $activitiesCompany = ActivityResource::collection(Activity::orderByDesc('created_at')->limit(10)->get());
        foreach ($activitiesCompany as $ac) {
            array_push($array, $ac);
        }
        //to  return with a lot of joins
        return $array;
    }



    //Companies that create a lot activities

    public function trendingCompanies()
    {
        return $company = CompanyResource::collection(Company::all());
        $filtered = $company->filter(function ($value) {
            return $value->nr_subscribers;
        });

        return $filtered->all();
    }

    //Show activities of company for current user
    public function getActivitiesOfCompany()
    {
        $company_following = CompanyFollower::where('user_id', auth('api')->user()->id)->inRandomOrder()->take(2)->get();
        $array = [];
        foreach ($company_following as $cf) {
            $company = Company::find($cf->company_id);
            $activities = ActivityResource::collection(Activity::where('user_id', $company->user_id)->limit(5)->orderByDesc('created_at')->get());
            if (count($activities) > 0) {
                $company->setAttribute('activities', $activities);
                array_push($array, $company);
            }
        }
        return  $array;
    }

    public function getCompanySubscribers($id){
        $company = Company::find($id);
        return $company->followers;
    }


}
