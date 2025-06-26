<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Api;

class OrderController extends Controller
{
    use ApiResponseTrail;

    public function makeneworder(Request $request)
    {
        //return $store_product = DB::table('store_products')->select('store_products.*')->where('product_id','=',2)->get();

        $validator = Validator::make($request->all(), [
            'location' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->apiResponse(null,$validator->messages(),false);
        }


        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $userprofile = json_decode($userprofile->content(),true);

        $cart_product = DB::table('cart_items')
            ->join('products','products.id','=','cart_items.product_id')
            ->select('products.id','products.name','products.description',
                'products.product_picture','products.price','cart_items.quantity')->get();

        if (!$cart_product->count()>0)
        {
            return $this->apiResponse(null,'You don\'t have products to make an order',false);
        }

        $totalPrice=0.0;

        foreach($cart_product as $product){
            $totalPrice += ($product->price * $product->quantity);
        }


        $orderitem = new OrderItemController();


       $order = Order::create([
           'user_id'=>$userprofile['id'],
           'total_price'=>$totalPrice,
           'location'=>$request->location,
       ]);

        if( $orderitem->getallorderproduct($order->id))
        {
            return $this->apiResponse(new OrderResource($order),'New Order Placed Successfully',true);
        }
        return $this->apiResponse(null,'Something went wrong',false);

    }

    public function getuserorders(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $userprofile = json_decode($userprofile->content(),true);

        $user_orders = DB::table('orders')->select('orders.*')
            ->where('user_id','=',$userprofile['id'])->get();

        $user_orders = OrderResource::collection($user_orders);

        if ($user_orders->count()>0)
        {
            return $this->apiResponse($user_orders,'This is your orders',true);
        }

        return $this->apiResponse(null,'You Don\'t have orders',false);
    }

    public function getpendingorders(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $userprofile = json_decode($userprofile->content(),true);

        $user_orders = DB::table('orders')->select('orders.*')
            ->where('user_id','=',$userprofile['id'])
            ->where('order_status','=','pending')->get();

        $user_orders = OrderResource::collection($user_orders);

        if ($user_orders->count()>0)
        {
            return $this->apiResponse($user_orders,'This is your orders',true);
        }

        return $this->apiResponse(null,'You Don\'t have orders',false);
    }

    public function getdeliveringorders(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $userprofile = json_decode($userprofile->content(),true);

        $user_orders = DB::table('orders')->select('orders.*')
            ->where('user_id','=',$userprofile['id'])
            ->where('order_status','=','delivering')->get();

        $user_orders = OrderResource::collection($user_orders);

        if ($user_orders->count()>0)
        {
            return $this->apiResponse($user_orders,'This is your orders',true);
        }

        return $this->apiResponse(null,'You Don\'t have orders',false);
    }

    public function getdeliveredorders(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $userprofile = json_decode($userprofile->content(),true);

        $user_orders = DB::table('orders')->select('orders.*')
            ->where('user_id','=',$userprofile['id'])
            ->where('order_status','=','delivered')->get();

        $user_orders = OrderResource::collection($user_orders);

        if ($user_orders->count()>0)
        {
            return $this->apiResponse($user_orders,'This is your orders',true);
        }

        return $this->apiResponse(null,'You Don\'t have orders',false);
    }


}
