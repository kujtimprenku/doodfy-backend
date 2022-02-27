<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\Validator;
use App\UserCategory;
use App\Company;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;
use App\Mail\verifyEmail;
use App\Mail\changePasswordEmail;
use App\Club;
use Illuminate\Support\Facades\Hash;
use App\CompanyFollower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login',
            'register',
            'registerCompany',
            'registerClub',
            'activation',
            'forgotPassword',
            'verifyToken',
            'newPassword',
            'emailCheck'
        ]]);
    }
    //Me method
    public function me(){
        return auth('api')->user();
    }
    // Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $credentials = $request->only(['email', 'password']);
        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 400);
        }

        $token = auth()->user()->createToken('authToken')->accessToken;
        // $user = User::where('email', $request->email)->first();
        // if(!empty($user)){
        //     if($user->status == 0) {
        //         return response()->json(['error' => 'Please check your inbox to confirm your email'], 400);
        //     }
        // }
        return response()->json([
            'token' => $token,
            'user' => auth()->user()->only(['role_id', 'username'])
        ], 200);
    }


    // Register user
    public function register(Request $request){
        $validator = Validator::make($request->user, [
            'username' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create($request->user);
        $user->profile_image = env('APP_PATH_UPLOADS') . "profile/profile_img.png";
        $user->role_id = 1;
        //$user->verifyToken = Str::random(50);
        $user->update();
        //$this->sendEmail($user);
        $categories = $request->categories;
        foreach ($categories as $cat) {
            UserCategory::insert(['user_id' => $user->id, 'category_id' => $cat['id']]);
        }

        //Auto subcribe doodfy page
        CompanyFollower::insert([
            'user_id' => $user->id,
            'company_id' => 1,
            'created_at' => now(),
        ]);

        auth()->login($user);
        $token = auth()->user()->createToken('authToken')->accessToken;
        return response()->json([
            'token' => $token,
            'user' => auth()->user()->only(['role_id', 'username'])
        ], 201);
    }
    // Register company
    public function registerCompany(Request $request){
        $validator = Validator::make($request->user, [
            'email' => 'required|string|email|max:255|unique:users',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create($request->user);
        $user->profile_image = env('APP_PATH_UPLOADS') . "profile/profile_img.png";
        $user->role_id = 2;
        $user->city_id = $request->company['city_id'];
        $user->update();

        $company_id = Company::insertGetId([
            'user_id' => $user->id,
            'country_id' => $request->company['country_id'],
            'city_id' => $request->company['city_id'],
            'firm' => $request->company['firm'],
            'street'=> $request->company['street'],
            'branch'=> $request->company['branch'],
            'website'=> $request->company['website'],
            'logo'=> env('APP_PATH_UPLOADS') . 'company/noimage.jpg',
            'created_at' => now(),
        ]);

        auth()->login($user);
        $token = auth()->user()->createToken('authToken')->accessToken;
        return response()->json([
            'token' => $token,
            'user' => ['role_id' => auth()->user()->role_id, 'company_id' => $company_id]
        ], 201);
    }

    /**
     * Log out
     */
    public function logout(){
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['success' => 'Logged out Successfully.'], 200);
    }


    public function sendEmail($thisUser){
        Mail::to($thisUser['email'])->send(new verifyEmail($thisUser));
    }

    public function changePasswordEmail($thisUser){
        Mail::to($thisUser['email'])->send(new changePasswordEmail($thisUser));
    }

    public function changePassword(Request $request){
        $user = auth('api')->user();
        $new_password  = $request->new_password;
        if(Hash::check($request->current_password, $user->password, [])){
            if($request->current_password == $request->new_password){
                return response()->json(['error'=> 'Password must differ from old password.'], 400);
            }
            elseif(!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)){
                return response()->json(['error'=> 'Password must have letter and number.'], 400);
            }
            else{
                $user->password = $new_password;
                $user->update();
                return response()->json(['message'=> 'Password changed succesfully!']);
            }
        }
        else {
            return response()->json(['error'=> 'You typed wrong password. Try again.'], 400);
        }
    }

    public function verifyToken($token){
        $user = User::where('verifyToken', $token)->first();
        if(!is_null($user)){
            $user->status = 1;
            $user->verifyToken = '';
            $user->update();
            return response()->json(['message'=> 'Successfully confirmed your email']);
        }
        else{
            return response()->json(['message'=> 'Invalid token']);
        }
    }

    public function forgotPassword(Request $request){
        $user = User::where('email', $request->email)->first();
        if (!is_null($user)) {
            $user->verifyToken = Str::random(50);
            $user->update();
            Mail::to($request->email)->send(new changePasswordEmail($user));
            return response()->json(['message'=> 'Successfully email sent']);
        }
        else {
            return response()->json(['message'=> 'This email dosen\'t exits.']);
        }
    }


    public function newPassword($token, Request $request){
        $user = User::where('verifyToken', $token)->first();
        if(!is_null($user)){
            $user->verifyToken = '';
            $user->password = $request->password;
            $user->update();
            return response()->json(['message'=> 'Successfully updated password']);
        }
        else{
            return response()->json(['message'=> 'Invalid token']);
        }
    }

    public function emailCheck(Request $request){
        $check = User::where('email', $request->email)->first();
        if(!empty($check)){
            return response()->json(['error' => 'This email already exists'], 400);
        }
        return [];
    }
}
