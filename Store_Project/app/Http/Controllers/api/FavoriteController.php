<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponseTrail;

    public function addtofavorite(Request $request)
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

        $isfound = Favorite::where(['product_id' => $request->product_id, 'user_id' => $user['id']]);

        if ($isfound->count() > 0)
        {
            return $this->apiResponse(null,'this product is in your favorite already',false);
        }

        $favorite = Favorite::create([
            'user_id' => $user['id'],
            'product_id' =>$request->product_id,
        ]);

        return $this->apiResponse($favorite,'this product added successfully to favorite',true);
    }

    public function deletefromfavorite(Request $request)
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

        $isfound = Favorite::where(['product_id' => $request->product_id, 'user_id' => $user['id']]);

        if ($isfound->count() == 0)
        {
            return $this->apiResponse(null,'this product isn\'t in your favorite',false);
        }

        $isfound->delete();

        return $this->apiResponse(null,'product remove from favorite',true);
    }

    public function getallfavoriteproduct(Request $request)
    {
        $authcontroller = new AuthController();

        $userprofile = $authcontroller->userProfile();

        if (!$userprofile) {
            return $this->apiResponse(null, 'user not exist', false);
        }

        $userprofile = json_decode($userprofile->content(), true);

        $user = User::find($userprofile['id']);

        $favorite_product = $user->products;

        if ($favorite_product->count()>0)
        {
            return $this->apiResponse($favorite_product, 'This is your favorite products',true);;
        }

        return $this->apiResponse(null, 'This user has no favorite products',false);
    }
}
