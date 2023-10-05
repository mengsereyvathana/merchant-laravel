<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exception;
use App\Models\products;
use App\Models\category;
use App\Models\User;
use App\Models\Cart;
use App\Models\order;
use App\Models\orderDetail;
use App\Models\scheme;
use App\Models\product_price_scheme;
use App\Models\slide;
use App\Http\Requests\AddListRequest;
use App\Http\Requests\UpdateSlideRequest;
use App\Http\Requests\addCartRequest;
use App\Http\Requests\UpdateListRequest;
use App\Http\Requests\ProductSchemePriceRequest;
use Illuminate\Support\Collection;

class AdminListController extends Controller
{
    public function user_scheme_price_list($pg = null)
    {
        $offset = 0;
        $limit  = 4;
        $tbl = product_price_scheme::all();
        //check user id
        if ($tbl) {
            //   $scheme_id =  $user->scheme_id;
            $pro_price_scheme = product_price_scheme::with('products')->get();

            if ($pg > 0) {
                $offset = ($pg - 1) * $limit;
            }
            $pg ? $data = product_price_scheme::with('products')->offset($offset)->limit($limit)->orderBy('id', 'DESC')->get()
                : $data = product_price_scheme::with('products')->with('products.category')->orderBy('id', 'DESC')->get();
            $total_page = count($pro_price_scheme);

            if (count($data) == 0) {
                $total_page = 0;
                $sum_page = 0;
            }
            if (($total_page - $offset) < $limit || empty($pg)) {
                $sum_page = $total_page;
            } else {
                $sum_page = $total_page - ($total_page - $offset) + $limit;
            }
            return response()->json([
                'success' => true,
                'data' => $data,
                'per_page' => $limit,
                'sum_page' => $sum_page,
                'total_page' => $total_page,
                'lenght' => ceil($total_page / $limit)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No Table product_price_scheme'
            ], 404);
        }
    }

    public function user_scheme_price_list_detail($id = null)
    {
        if ($id != null) {
            $check = product_price_scheme::where('id', $id)->first();
            if ($check) {
                $de_product = product_price_scheme::where('id', $id)->with('products')->first();
                if ($de_product) {
                    return response()->json([
                        'success' => true,
                        'data' => $de_product
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'The process has been an error.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The product ID not found.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The ID must not be null'
            ], 400);
        }
    }
    public function user_scheme_price_list_update(Request $req, $id = null)
    {
        $old_product = product_price_scheme::where('id', $id)->first();
        $product_id = $old_product->product_id;
        $product = products::where('id', $product_id)->first();
        $product_price = $product->price;
        if ($old_product) {
            if ($req->product_id == null) {
                $new_product_id = $old_product->product_id;
            } else {
                $new_product_id = $req->product_id;
            }
            if ($req->scheme_id == null) {
                $new_scheme_id = $old_product->scheme_id;
            } else {
                $new_scheme_id = $req->scheme_id;
            }
            if ($req->action == null) {
                $new_action = $old_product->action;
            } else {
                $new_action = $req->action;
            }
            if ($req->unit_price == null) {
                $new_unit_price =  $old_product->unit_price;
                $margin = $old_product->margin;
            } else {
                $new_unit_price =  $req->unit_price;
                $margin = $product_price - $req->unit_price;
            }


            if ($req->product_id != null && $req->scheme_id != null) {
                if ($req->product_id != $old_product->product_id || $req->scheme_id != $old_product->scheme_id) {
                    $check_update = product_price_scheme::where([['product_id', $new_product_id], ['scheme_id', $new_scheme_id]])->first();
                } else {
                    $check_update = false;
                }
            } else {
                $check_update = false;
            }
            if ($req->product_id != null &&  $req->scheme_id == null) {
                if ($req->product_id != $old_product->product_id) {
                    $check_update = product_price_scheme::where([['product_id', $new_product_id], ['scheme_id', $new_scheme_id]])->first();
                } else {
                    $check_update = false;
                }
            }
            if ($req->product_id == null &&  $req->scheme_id != null) {

                if ($req->scheme_id != $old_product->scheme_id) {
                    $check_update = product_price_scheme::where([['product_id', $new_product_id], ['scheme_id', $new_scheme_id]])->first();
                } else {
                    $check_update = false;
                }
            }

            if (!$check_update) {
                $update =  product_price_scheme::where('id', $id)->update(['product_id' => $new_product_id, 'scheme_id' => $new_scheme_id, 'unit_price' => $new_unit_price, 'action' => $new_action, 'margin' => $margin]);
                $data =  product_price_scheme::where('id', $id)->first();
                if ($update) {
                    return response()->json([
                        'success' => true,
                        'message' => 'The prouct has been updated.',
                        'data' => $data
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'The process has an error.',
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The product has already been created.',
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The product ID not found.'
            ], 400);
        }
    }
    public function user_scheme_price_list_add(ProductSchemePriceRequest $req)
    {
        $pro_id = $req->pro_id;
        $scheme_id = $req->scheme_id;
        $unit_price = $req->unit_price;
        $check = product_price_scheme::where('scheme_id', $scheme_id)->where('product_id', $pro_id)->first();
        if ($check) {
            return response()->json([
                'success' => false,
                'message' => 'This product scheme price has already been created.',
            ], 400);
        } else {
            $get_product = products::where('id', $pro_id)->first();
            if ($get_product) {
                $price = $get_product->price;
                $margin = $price - $unit_price;
                $margin;
                $insert_pro_scheme_price = new product_price_scheme;
                $insert_pro_scheme_price->product_id = $pro_id;
                $insert_pro_scheme_price->scheme_id = $scheme_id;
                $insert_pro_scheme_price->unit_price = $unit_price;
                $insert_pro_scheme_price->margin = $margin;
                $data = $insert_pro_scheme_price->save();
                $get_data = $insert_pro_scheme_price->refresh();
                if ($data) {
                    return response()->json([
                        'success' => true,
                        'message' => 'The Product price scheme has been created successfully',
                        'data' => $get_data
                    ], 400);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insert Error'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The product ID not found'
                ], 400);
            }
        }
    }
    public function user_scheme_price_list_delete($id = null)
    {
        if ($id != null) {
            $check = product_price_scheme::where('id', $id)->first();
            if ($check) {
                $del_product = product_price_scheme::where('id', $id)->delete();
                if ($del_product) {
                    return response()->json([
                        'success' => true,
                        'message' => 'The product has been an deleted.'
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'The process has been an error.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The product ID not found.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The ID must not be null'
            ], 400);
        }
    }
}
