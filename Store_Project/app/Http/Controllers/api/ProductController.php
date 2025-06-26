<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponseTrail;

    public function index()
    {
        $product = ProductResource::collection(Product::get());

        return $this->apiResponse($product,'this is all products',true);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->apiResponse(null,$validator->messages(),false);
        }

        $product = Product::create($request->all());

        if ($product)
        {
            return $this->apiResponse(new ProductResource($product),'New Product Added',true);
        }

        return  $this->apiResponse(null,'Product Added failed',false);
    }

    public function find(Request $request)
    {
        $product = Product::find($request->product_id);

        if ($product)
        {
            return $this->apiResponse(new ProductResource($product),'This is The Product You Want',true);
        }

        return $this->apiResponse(null,'Product not Found',false);
    }

    public function search(Request $request)
    {
        $product = DB::table('products')->
        where('name','LIKE',$request->name.'%')->get();


        if (!$product->count()>0)
        {
            return $this->apiResponse(null,'No product found',false);
        }

        $product = ProductResource::collection($product);

        return $this->apiResponse($product,'product Found',true);
    }

}
