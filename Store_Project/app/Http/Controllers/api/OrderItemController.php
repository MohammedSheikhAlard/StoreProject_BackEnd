<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    use ApiResponseTrail;

    public function getallorderproduct($order_id)
    {
        $cart_product = DB::table('cart_items')
            ->join('products','products.id','=','cart_items.product_id')
            ->select('products.id','products.name','products.description',
                'products.product_picture','products.price','cart_items.quantity')->get();

        foreach($cart_product as $product){
            OrderItem::create([
                'order_id'=>$order_id,
                'product_id' => $product->id,
                'quantity'=> $product->quantity,
            ]);
            $store_products = DB::table('store_products')->select('store_products.*')
                ->where('product_id','=',$product->id)->get();

            foreach($store_products as $store_product){

            $new_quantity = $store_product->quantity - $product->quantity;

            StoreProduct::where('id',$store_product->id)->update(['quantity'=>$new_quantity]);

            }
        }

        DB::table('cart_items')->delete();

        return true;
    }

    public function getordereditem(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $order_products = DB::table('order_items')
            ->join('products','products.id','=','order_items.product_id')
            ->select('products.id','products.name','products.description',
                'products.product_picture','products.price','order_items.quantity')->where('order_id','=',$request->order_id)->get();

        if ($order_products->count()>0)
        {
            return $this->apiResponse($order_products, 'This is your order products',true);
        }

        return $this->apiResponse(null, 'there is no products for this order_id',false);

    }

}
