<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Cart_itemController extends Controller
{
    use ApiResponseTrail;

    public function addtocart(Request $request)
    {
        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->apiResponse(null, 'the Product ID is not exist', false);
        }

        $store_products = DB::table('store_products')->select('store_products.*')
            ->where('product_id','=',$request->product_id)->get();

        foreach($store_products as $store_product){


        if ($store_product->quantity < $request->quantity)
        {
            return $this->apiResponse(null,'the quantity you entered is no\'t valid',false);
        }

        }
        $authcontroller = new AuthController();

        $user = $authcontroller->userProfile();


        if (!$user) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $user = json_decode($user->content(), true);

        $isfound = CartItem::where(['product_id' => $request->product_id, 'user_id' => $user['id']]);

        if ($isfound->count() > 0)
        {
            return $this->apiResponse(null,'this product is in your cart already',false);
        }

        $cart_item =CartItem::create([
            'user_id' => $user['id'],
            'product_id' =>$request->product_id,
            'quantity' => $request->quantity,
        ]);

        return $this->apiResponse($cart_item,'this product added to your cart',true);
    }

    public function deletefromcart(Request $request)
    {
        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->apiResponse(null, 'the Product ID is not exist', false);
        }

        $authcontroller = new AuthController();

        $user = $authcontroller->userProfile();

        if (!$user) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $user = json_decode($user->content(), true);

        $isfound = CartItem::where(['product_id' => $request->product_id, 'user_id' => $user['id']]);

        if ($isfound->count() == 0)
        {
            return $this->apiResponse(null,'this product isn\'t in your cart',false);
        }

        $isfound->delete();

        return $this->apiResponse(null,'product remove from cart',true);
    }

    public function getallcartproduct(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $cart_product = DB::table('cart_items')
             ->join('products','products.id','=','cart_items.product_id')
             ->select('products.id','products.name','products.description',
                 'products.product_picture','products.price','cart_items.quantity')->get();

        if ($cart_product->count()>0)
        {
            return $this->apiResponse($cart_product, 'This is your cart products',true);
        }

        return $this->apiResponse(null, 'This user has no products in his cart',false);
    }
}
