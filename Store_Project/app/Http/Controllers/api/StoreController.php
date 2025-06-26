<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\StoreResource;
use App\Models\Admin;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Collection\Collection;

class StoreController extends Controller
{
    use ApiResponseTrail;

    public function index()
    {
        $stores = StoreResource::collection(Store::get());

        return $this->apiResponse($stores,'This is All Stores',true);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'admin_id' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->apiResponse(null,$validator->messages(),false);
        }

        $admin = Admin::find($request->admin_id);

        if (!$admin->count()>0)
        {
            return $this->apiResponse(null,'admin id not exist',false);
        }

        $store = Store::create($request->all());

        if ($store)
        {
            return $this->apiResponse(new StoreResource($store),'New store Added',true);
        }

        return  $this->apiResponse(null,'store Added failed',false);
    }

    public function find(Request $request)
    {
        $store = Store::find($request->store_id);

        if ($store)
        {
            return $this->apiResponse(new StoreResource($store),'store Found',true);
        }

        return $this->apiResponse(null,'store not Found',false);
    }

    public function search(Request $request)
    {
        $store = DB::table('stores')->
            where('name','LIKE',$request->name.'%')->get();


        if (!$store->count()>0)
        {
            return $this->apiResponse(null,'No store found',false);
        }

        $store = StoreResource::collection($store);


        return $this->apiResponse($store,'store Found',true);
    }
}
