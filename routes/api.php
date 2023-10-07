<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminListController;
use App\Http\Controllers\Admin\AdminUserController;
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
    Route::get('/user_scheme_price_list_by_category', [ListController::class, 'user_scheme_price_list_by_category']);
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
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/list', [AdminListController::class, 'list']);
        Route::post('/add_list', [AdminListController::class, 'add_list']);
        Route::put('/update_list/{id}', [AdminListController::class, 'update_list']);
        Route::delete('delete_list/{id?}', [AdminListController::class, 'delete']);
        Route::get('/user_scheme_price_list', [AdminListController::class, 'user_scheme_price_list']);
        Route::get('/user_scheme_price_list_detail/{id?}', [AdminListController::class, 'user_scheme_price_list_detail']);
        Route::delete('/user_scheme_price_list_delete/{id?}', [AdminListController::class, 'user_scheme_price_list_delete']);
        Route::post('/user_scheme_price_list_add', [AdminListController::class, 'user_scheme_price_list_add']);
        Route::put('/user_scheme_price_list_update/{id?}', [AdminListController::class, 'user_scheme_price_list_update']);
        Route::get('/list_ordered', [AdminListController::class, 'list_ordered']);
        Route::delete('/List_ordered_delete/{invoice?}', [AdminListController::class, 'List_ordered_delete']);
        Route::get('search_product', [AdminListController::class, 'search_product']);
        Route::get('search_scheme', [AdminListController::class, 'search_scheme']);
        Route::get('search_slide', [AdminListController::class, 'search_slide']);
        Route::get('search_order', [AdminListController::class, 'search_order']);
        Route::post('add_category', [AdminListController::class, 'add_category']);
        Route::get('list_category', [AdminListController::class, 'list_category']);
        Route::delete('delete_category/{id?}', [AdminListController::class, 'delete_category']);
        Route::get('detail_category/{id?}', [AdminListController::class, 'detail_category']);
        Route::put('update_category/{id?}', [AdminListController::class, 'update_category']);
        Route::post('/add_slide', [AdminListController::class, 'add_slide']);
        Route::get('/list_slide', [AdminListController::class, 'list_slide']);
        Route::get('/detail_slide/{slide_id?}', [AdminListController::class, 'detail_slide']);
        Route::delete('/delete_slide', [AdminListController::class, 'delete_slide']);
        Route::put('/update_slide/{id?}', [AdminListController::class, 'update_slide']);
        Route::get('search_category', [AdminListController::class, 'search_category']);
        Route::post('/logout', [AdminUserController::class, 'logout'])->middleware('auth::sanctum'); //check uathorization use if(auth()->check())
    });
    //auth
    Route::post('login', [AdminUserController::class, 'login']);
});
    

// Route::apiResource('list',ListController::class);
