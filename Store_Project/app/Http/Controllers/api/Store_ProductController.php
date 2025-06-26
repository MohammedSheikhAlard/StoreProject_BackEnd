<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Store_ProductController extends Controller
{
    use ApiResponseTrail;

    public function index()
    {
        $storeProduct = StoreProductResource::collection(StoreProduct::get());

        return $this->apiResponse($storeProduct,'This is All Store Products',true);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
            'product_id' => 'required|unique:store_products',
            'quantity' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->apiResponse(null,$validator->messages(),false);
        }

        $storeProduct = StoreProduct::create($request->all());

        if ($storeProduct)
        {
            return $this->apiResponse(new StoreProductResource($storeProduct),'New Store Product Added',true);
        }

        return  $this->apiResponse(null,'store product Added failed',false);
    }

    public function findOneProduct(Request $request)
    {
        $storeProduct = DB::table('store_products')
            ->join('products','products.id','=','store_products.product_id')
            ->select('products.id','products.name','products.description',
                'products.product_picture','products.price','store_products.quantity')->where('product_id','=',$request->product_id)->get();

        if ($storeProduct)
        {
            return $this->apiResponse($storeProduct,'store product Found',true);
        }

        return $this->apiResponse(null,'store product not Found',false);
    }

    public function findAllStoreProduct(Request $request)
    {
        $store = Store::find($request->store_id);

        if (!$store)
        {
            return $this->apiResponse(null,'store ID not found',false);
        }

        $storeProduct = $store->products;

        if ($storeProduct) {
            return $this->apiResponse($storeProduct, 'store products Found', true);
        }

        return $this->apiResponse(null, 'store product not Found', false);
    }


}
