<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\ProductResource;
use App\Models\Admin;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use ApiResponseTrail;

    public function index()
    {
        $admins = AdminResource::collection(Admin::get());

        return $this->apiResponse($admins,'This is All Admins',true);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->apiResponse(null,$validator->messages(),false);
        }

        $admin = Admin::create($request->all());

        if ($admin)
        {
            return $this->apiResponse(new AdminResource($admin),'New admin Added',true);
        }

        return  $this->apiResponse(null,'Admin Added failed',false);
    }

    public function find(Request $request)
    {
        $admin = Admin::find($request->admin_id);

        if ($admin)
        {
            return $this->apiResponse(new AdminResource($admin),'Admin Found',true);
        }

        return $this->apiResponse(null,'Admin not Found',false);
    }
}
