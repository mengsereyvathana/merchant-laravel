<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController as UserAuthController;
use App\Http\Controllers\ProductController as UserProductController;
use App\Http\Controllers\CategoryController as UserCategoryController;
use App\Http\Controllers\ProductSchemeController as UserProductSchemeController;
use App\Http\Controllers\CartController as UserCartController;
use App\Http\Controllers\OrderController as UserOrderController;
use App\Http\Controllers\SlideController as UserSlideController;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSchemeController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
    ], 200);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    //product
    Route::get('product/{id?}', [UserProductController::class, 'show']);

    //product scheme
    Route::get('/product_scheme', [UserProductSchemeController::class, 'index']);
    Route::get('/product_scheme_detail', [UserProductSchemeController::class, 'show']);
    Route::get('/product_scheme_by_category', [UserProductSchemeController::class, 'productByCategory']);

    //cart
    Route::get('/cart', [UserCartController::class, 'index']);
    Route::post('/add_to_cart', [UserCartController::class, 'addToCart']);
    Route::post('/subtract_from_cart', [UserCartController::class, 'subtractFromCart']);
    Route::delete('/delete_cart', [UserCartController::class, 'deleteCart']);

    //order
    Route::get('/order', [UserOrderController::class, 'index']);
    Route::post('/order', [UserOrderController::class, 'store']);
    Route::post('/order_detail', [UserOrderController::class, 'showOrderDetail']);
});

//product
Route::get('/product/{pg?}', [UserProductController::class, 'index']);
Route::get('/product/search/{pg?}', [UserProductSchemeController::class, 'search']);

//cart
Route::get('/category', [UserCategoryController::class, 'index']);


//slide
Route::get('/slide', [UserSlideController::class, 'index']);
Route::get('/slide/{slide_id?}', [UserSlideController::class, 'show']);


//auth
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/create_user_with_phone', [UserAuthController::class, 'createUserWithPhone']);
Route::post('/log_with_phone', [UserAuthController::class, 'loginWithPhone']);
Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/verify_email_otp', [UserAuthController::class, 'verifyEmailOtp']);
Route::post('/resend_otp', [UserAuthController::class, 'resendOtp']);
Route::post('/change_password', [UserAuthController::class, 'changePassword']);
Route::post('/forgot', [UserAuthController::class, 'forgotPassword']);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        //product
        Route::get('/product', [ProductController::class, 'index']);
        Route::get('/product/search', [ProductController::class, 'search']);
        Route::get('/product_by_category', [ProductController::class, 'productByCategory']);
        Route::post('/product', [ProductController::class, 'store']);
        Route::get('product/{id?}', [ProductController::class, 'show']);
        Route::put('/product/{id}', [ProductController::class, 'update']);
        Route::delete('product/{id?}', [ProductController::class, 'destroy']);

        //product scheme
        Route::get('/product_scheme', [ProductSchemeController::class, 'index']);
        Route::get('/product_scheme/search', [ProductSchemeController::class, 'search']);
        Route::get('/product_scheme/{id?}', [ProductSchemeController::class, 'show']);
        Route::post('/product_scheme', [ProductSchemeController::class, 'store']);
        Route::put('/product_scheme/{id?}', [ProductSchemeController::class, 'update']);
        Route::delete('/product_scheme/{id?}', [ProductSchemeController::class, 'destroy']);

        //slide
        Route::get('/slide', [SlideController::class, 'index']);
        Route::post('/slide', [SlideController::class, 'store']);
        Route::get('/slide/search', [SlideController::class, 'search']);
        Route::get('/slide/{slide_id?}', [SlideController::class, 'show']);
        Route::put('/slide/{id?}', [SlideController::class, 'update']);
        Route::delete('/slide', [SlideController::class, 'destroy']);

        //category
        Route::get('/category', [CategoryController::class, 'index']);
        Route::get('/category/search', [CategoryController::class, 'search']);
        Route::post('/category', [CategoryController::class, 'store']);
        Route::get('/category/{id?}', [CategoryController::class, 'show']);
        Route::put('/category/{id?}', [CategoryController::class, 'update']);
        Route::delete('/category/{id?}', [CategoryController::class, 'destroy']);

        //order
        Route::get('/order', [OrderController::class, 'index']);
        Route::get('/order/search', [OrderController::class, 'search']);
        Route::delete('/order/{invoice?}', [OrderController::class, 'destroy']);

        //auth
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    //auth
    Route::post('/login', [AuthController::class, 'login']);
});
