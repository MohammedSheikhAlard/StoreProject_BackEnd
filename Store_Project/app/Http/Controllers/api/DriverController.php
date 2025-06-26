<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\DriverResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class DriverController extends Controller
{

    use ApiResponseTrail;

    public function __construct(){
       Config::set('auth.defaults.guard','driver-api');
    }


    public function login(Request $request){
        $validator= Validator::make($request->all(),[
            'name' => 'required',
            'phone' => 'required',
            'password'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],422);
        }

        if(!$token = auth()->attempt($validator->validated())){
            return response()->json(['error'=>'HElloo'],401);
        }
        return $this->createNewToken($token);

    }


    public function register(Request $request){
        $validator= Validator::make($request->all(),[
            'name' => 'required|between:2,20',
            'phone' => 'required|unique:drivers',
            'password'=>'required|min:8'
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],400);
        }

        $driver = Driver::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password'=>bcrypt($request->password),
        ]);


        //return $this->apiResponse($driver,'driver created Successfully',true);

       if(! $token = auth()->attempt($validator->validated())){
           return response()->json(['error'=>'Unauthorized'],401);
       }

       return response()->json([
           'status'=>true,
           'access_token' => $token,
           'token_type' => 'bearer',
           //'expires_in' => auth()->factory()->getTTL() * 60,
           'driver' => $driver
       ]);

    }


    public function logout(){

        auth()->logout();

        return response()->json(['massage' => 'driver successfully signed out' ]);

    }

    public function refresh(){
        return $this->createNewToken(auth()->refresh());
    }

    public function driverProfile(){
        return response()->json(auth()->user());
    }

    public function update_driver_profile(Request $request)
    {

        $validator= Validator::make($request->all(),[
            'phone' => 'unique:users',
            'password' => 'min:8',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],400);
        }


        $driver = JWTAuth::toUser($request->token);

        if (!$driver)
        {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $authcontroller = new AuthController();

        $driverprofile = $authcontroller->driverProfile();

        if (!$driverprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }


        $driverprofile = json_decode($driverprofile->content(),true);

        $driver = Driver::findorFail($driverprofile['id']);

        $driver ->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return $this->apiResponse($driver,'Driver has been updated successfully',true);

    }

    protected function createNewToken($token){
        return response()->json([
            'status'=>true,
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'driver' => DriverResource::make(auth()->user())
        ]);
    }


    public  function take_order(Request $request)
    {
        $order = Order::find($request->order_id);

        if ($order==null)
        {
            return $this->apiResponse(null,'Order not Found',false);
        }

        $driver = JWTAuth::toUser($request->token);

        if ($driver==null)
        {
            return $this->apiResponse(null,'Driver not Found',false);
        }

        $order ->update([
            'driver_id'=>$driver->id,
            'order_status' => 'delivering'
        ]);

        return $this->apiResponse(new OrderResource($order),'We are delivering your order',true);

    }

    public  function delivered_order(Request $request)
    {
        $order = Order::find($request->order_id);

        if ($order==null)
        {
            return $this->apiResponse(null,'Order not Found',false);
        }

        $driver = JWTAuth::toUser($request->token);

        if ($driver==null)
        {
            return $this->apiResponse(null,'Driver not Found',false);
        }

        $order ->update([
            'order_status' => 'delivered'
        ]);

        return $this->apiResponse(new OrderResource($order),'Your Order has been delivered',true);

    }

    public function showallorders(Request $request)
    {
        $driver = JWTAuth::toUser($request->token);

        if ($driver==null)
        {
            return $this->apiResponse(null,'Driver not Found',false);
        }

        $driver_orders = DB::table('orders')->select('orders.*')
            ->where('order_status','=','pending')->get();

        $driver_orders = OrderResource::collection($driver_orders);

        if ($driver_orders->count()>0)
        {
            return $this->apiResponse($driver_orders,'This is your orders',true);
        }

        return $this->apiResponse(null,'You Don\'t have orders',false);
    }
}
