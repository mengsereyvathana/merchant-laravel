<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminListController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
    ], 200);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    //jasldfjasldjf
    // Route::post('/logout',[UserController::class,'logout'])->name('auth.logout');
    Route::get('/user_scheme_price_list', [ListController::class, 'user_scheme_price_list']);
    Route::post('/add_list', [ListController::class, 'add_list']);
    Route::put('/update_list/{id}', [ListController::class, 'update_list']);
    Route::get('detail_list/{id?}', [ListController::class, 'detail_list']);
    Route::delete('delete_list/{id?}', [ListController::class, 'delete_list']);
    Route::post('/sub_to_cart', [ListController::class, 'sub_to_cart']);
    Route::delete('/delete_cart', [ListController::class, 'delete_cart']);
    Route::post('/order', [ListController::class, 'order']);
    Route::post('/order_detail', [ListController::class, 'order_detail']);
    Route::post('/add_to_cart', [ListController::class, 'add_to_cart']);
    Route::get('/list_cart', [ListController::class, 'list_cart']);
    Route::get('/list_ordered', [ListController::class, 'list_ordered']);
    Route::get('/user_scheme_price_list_detail', [ListController::class, 'user_scheme_price_list_detail']);
    Route::post('/user_scheme_price_list_add', [ListController::class, 'user_scheme_price_list_add']);
    Route::delete('/user_scheme_price_list_delete/{scheme_id?}', [ListController::class, 'user_scheme_price_list_delete']);
    Route::get('/user_scheme_price_list_by_category', [ListController::class, 'user_scheme_price_list_by_category']);
    Route::post('/add_slide', [ListController::class, 'add_slide']);
    Route::delete('/delete_slide', [ListController::class, 'delete_slide']);
    Route::post('/add_category', [ListController::class, 'add_category']);
    Route::put('/update_slide', [ListController::class, 'update_slide']);
});

Route::get('/show_cart/{pg?}', [ListController::class, 'show_cart']);

Route::get('/list/{pg?}', [ListController::class, 'list']);
Route::get('/search/{pg?}', [ListController::class, 'search']);
Route::get('/list_slide', [ListController::class, 'list_slide']);
Route::get('/detail_slide/{slide_id?}', [ListController::class, 'detail_slide']);


Route::post('/login', [UserController::class, 'login']);
Route::post('/admin_login', [UserController::class, 'admin_login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [UserController::class, 'register']);
Route::post('/verify_email_otp', [UserController::class, 'verify_email_otp']);
Route::post('/resend_otp', [UserController::class, 'resend_otp']);
Route::post('/change_password', [UserController::class, 'change_password']);
Route::post('forgot', [UserController::class, 'forgot']);
Route::post('/log_with_phone', [UserController::class, 'log_with_phone']);
Route::post('/create_user_with_phone', [UserController::class, 'create_user_with_phone']);
Route::get('/list_category', [ListController::class, 'list_category']);


Route::group(['prefix' => 'admin'], function () {
    //list
    Route::get('/list', [AdminListController::class, 'list']);
    Route::post('/add_list', [AdminListController::class, 'add_list']);
    Route::put('/update_list/{id}', [AdminListController::class, 'update_list']);
    Route::delete('delete_list/{id?}', [AdminListController::class, 'delete']);
    Route::get('/user_scheme_price_list/{user?}/{pg?}', [AdminListController::class, 'user_scheme_price_list']);
    Route::get('/user_scheme_price_list_detail/{id?}', [AdminListController::class, 'user_scheme_price_list_detail']);
    Route::delete('/user_scheme_price_list_delete/{id?}', [AdminListController::class, 'user_scheme_price_list_delete']);
    Route::post('/user_scheme_price_list_add', [AdminListController::class, 'user_scheme_price_list_add']);
    Route::put('/user_scheme_price_list_update/{id?}', [AdminListController::class, 'user_scheme_price_list_update']);
    Route::get('/list_ordered/{id?}', [AdminListController::class, 'list_ordered']);

    //auth
    Route::post('login', [AdminUserController::class, 'login']);
    Route::post('/logout', [AdminUserController::class, 'logout'])->middleware('auth::sanctum'); //check uathorization use if(auth()->check())

});
    

// Route::apiResource('list',ListController::class);
