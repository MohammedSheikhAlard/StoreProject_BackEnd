<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponseTrail;

    public function __construct(){
        $this->middleware('auth:api',['except' => ['login','register']]);
    }


    public function login(Request $request){
        $validator= Validator::make($request->all(),[
            'phone' => 'required',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],422);
        }

        if(!$token = auth()->attempt($validator->validated())){
            return response()->json(['error'=>'Unauthorized'],401);
        }
        return $this->createNewToken($token);

    }


    public function register(Request $request){
        $validator= Validator::make($request->all(),[

            'first_name' => 'required|between:2,20',
            'last_name' => 'required|between:2,20',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],400);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' =>$request->last_name,
            'phone' =>$request->phone,
            'password' => bcrypt($request->password),
        ]);


        if(! $token = auth()->attempt($validator->validated())){
            return response()->json(['error'=>'Unauthorized'],401);
        }

        return response()->json([
            'status'=>true,
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ]);

    }


    public function logout(){

         auth()->logout();

        return response()->json(['massage' => 'User successfully signed out' ]);

    }

    public function refresh(){
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile(){
        return response()->json(auth()->user());
    }

    public function update_user_profile(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'phone' => 'unique:users',
            'password' => 'min:8',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],400);
        }


        $user = JWTAuth::toUser($request->token);

        if (!$user)
        {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }


        $userprofile = json_decode($userprofile->content(),true);

        $user = User::findorFail($userprofile['id']);

        $user ->update([
            'first_name' => $request->first_name,
            'last_name' =>$request->last_name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            //'location' => $request->location,
            'profile_picture' => $request ->profile_picture,
        ]);

        return $this->apiResponse($user,'User has been updated successfully',true);


//        $authcontroller = new AuthController();
//
//        $user = $authcontroller->userProfile();
//
//       if (!$user)
//       {
//           return $this->apiResponse(null,'this user is not exist',false);
//       }
//
//        $user = json_decode($user->content(),true);
//
//       $new_user = User::findorFAil($user['id']);
//
//       return $this->apiResponse($new_user,'This is user ',true);
//


    }

    protected function createNewToken($token){
        return response()->json([
            'status'=>true,
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }


}
