<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\Cart_itemController;
use App\Http\Controllers\api\DriverController;
use App\Http\Controllers\api\FavoriteController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\OrderItemController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\Store_ProductController;
use App\Http\Controllers\api\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
],function (){

    Route::post('/login',[AuthController::class,'login']);
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/refresh',[AuthController::class,'refresh']);
    Route::get('/user-profile',[AuthController::class,'userProfile']);

});

Route::group([
    'prefix' => 'driver'
],function (){

    Route::post('/login',[DriverController::class,'login']);
    Route::post('/register',[DriverController::class,'register']);
});



Route::group([
    'middleware' => ['jwt.role:driver','jwt.auth'],
    'prefix' => 'driver'
],function (){

    Route::post('/logout',[DriverController::class,'logout']);
    Route::post('/refresh',[DriverController::class,'refresh']);
    Route::get('/driver-profile',[DriverController::class,'driverProfile']);
    Route::post('/takeorder',[DriverController::class,'take_order']);
    Route::post('/deliveredorder',[DriverController::class,'delivered_order']);
    Route::post('/showallorders',[DriverController::class,'showallorders']);

});

Route::middleware(['jwt.verify'])->group(function (){

    Route::post('auth/update_user_profile',[AuthController::class,'update_user_profile']);

//////////////////////////////////////////////////////////////////////////


    Route::post('findStoreProduct',[Store_ProductController::class,'findOneProduct']);


//////////////////////////////////////////////////////////////////////////

    Route::post('getallfavoriteproduct',[FavoriteController::class,'getallfavoriteproduct']);

    Route::post('addtofavorite',[FavoriteController::class,'addtofavorite']);

    Route::post('deletefromfavorite',[FavoriteController::class,'deletefromfavorite']);

//////////////////////////////////////////////////////////////////////////


    Route::post('addtocart',[Cart_itemController::class,'addtocart']);

    Route::post('deletefromcart',[Cart_itemController::class,'deletefromcart']);

    Route::post('getallcartproduct',[Cart_itemController::class,'getallcartproduct']);

//////////////////////////////////////////////////////////////////////////


    //Route::post('getallorderproduct',[OrderItemController::class,'getallorderproduct']);

    Route::post('getordereditem',[OrderItemController::class,'getordereditem']);

//////////////////////////////////////////////////////////////////////////

    Route::post('makeneworder',[OrderController::class,'makeneworder']);

    Route::post('getuserorders',[OrderController::class,'getuserorders']);

    Route::post('getpendingorders',[OrderController::class,'getpendingorders']);

    Route::post('getdeliveringorders',[OrderController::class,'getdeliveringorders']);

    Route::post('getdeliveredorders',[OrderController::class,'getdeliveredorders']);

});

                    // This Routes For Admin

////////////////////////////////////////////////////////////////////////


Route::get('products',[ProductController::class,'index']);

Route::post('addProduct',[ProductController::class,'insert']);

Route::post('findProduct',[ProductController::class,'find']);

Route::post('searchProduct',[ProductController::class,'search']);


////////////////////////////////////////////////////////////////////////

Route::get('admins',[AdminController::class,'index']);

Route::post('addAdmin',[AdminController::class,'insert']);

Route::post('findAdmin',[AdminController::class,'find']);

////////////////////////////////////////////////////////////////////////

Route::get('stores',[StoreController::class,'index']);

Route::post('addStore',[StoreController::class,'insert']);

Route::post('findStore',[StoreController::class,'find']);

Route::post('searchStore',[StoreController::class,'search']);

////////////////////////////////////////////////////////////////////////

Route::get('storeProducts',[Store_ProductController::class,'index']);

Route::post('addStoreProduct',[Store_ProductController::class,'insert']);

Route::post('findAllStoreProduct',[Store_ProductController::class,'findAllStoreProduct']);



////////////////////////////////////////////////////////////////////////






