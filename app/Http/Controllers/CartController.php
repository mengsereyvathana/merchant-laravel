<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductScheme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    public function index(Request $req)
    {
        $data = Cart::where('user_id', $req->user_id)->with('products')->get();
        if (count($data) > 0) {
            // return 'g';
            for ($i = 0; $i < count($data); $i++) {
                $total = $data[$i]->total;
                $total_qty = $data[$i]->qty;
                $array[] = $total;
                $array_qty[] = $total_qty;
            };
            $numbers = new Collection($array);
            $numbers_qty = new Collection($array_qty);
            $amount = $numbers->sum();
            $amount_qty = $numbers_qty->sum();
            return response()->json([
                'success' => true,
                'data' => $data,
                'amount' => $amount,
                'amount_qty' => $amount_qty
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 200);
        }
    }

    public function addToCart(AddToCartRequest $req)
    {
        $user = User::where('id', $req->user_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user'
            ], 400);
        } else {
            $scheme_id = $user->scheme_id;
            $product_price_scheme = ProductScheme::where('scheme_id', $scheme_id)->where('product_id', $req->product_id)->first();
            if (!$product_price_scheme) {
                return response()->json([
                    'success' => false,
                    'message' => 'No product'
                ], 400);
            }

            $scheme_price = $product_price_scheme->unit_price;
        }

        $find_cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->get();
        if (count($find_cart) == 0) {
            // return 'g';
            $user_id = $req->user_id;
            $product_id = $req->product_id;
            $product = Product::where('id', $req->product_id)->first();
            $total = $scheme_price;
            $tbl_cart = new Cart;
            $tbl_cart->user_id = $user_id;
            $tbl_cart->product_id = $product_id;
            $tbl_cart->qty = 1;
            $tbl_cart->unit_price = $scheme_price;
            $tbl_cart->total = $total;
            $result = $tbl_cart->save();
            $get_result = $tbl_cart->refresh();
        } else {
            $cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->first();
            $up_qty = $cart->qty;
            Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->update(['qty' => $up_qty + 1]);
            $getCart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->with('products')->first();
            $qty = $getCart->qty;
            $price = $scheme_price;
            //    return $price;
            $total = $price * $qty;
            //    return $total;
            $result = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->update(['total' => $total]);
            $get_result = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->first();
        }
        // else{

        // }
        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $get_result
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'fail connect to table cart'
            ], 400);
        }
    }

    public function subtractFromCart(AddToCartRequest $req)
    {
        $user = User::where('id', $req->user_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user'
            ], 400);
        } else {
            $scheme_id = $user->scheme_id;
            $product_price_scheme = ProductScheme::where('scheme_id', $scheme_id)->where('product_id', $req->product_id)->first();
            if (!$product_price_scheme) {
                return response()->json([
                    'success' => false,
                    'message' => 'No product'
                ], 400);
            }

            $scheme_price = $product_price_scheme->unit_price;
        }

        $find_cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->first();

        if ($find_cart) {
            $cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->first();
            $up_qty = $cart->qty;
            Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->update(['qty' => $up_qty - 1]);
            $getCart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->with('products')->first();
            $qty = $getCart->qty;
            $price = $scheme_price;
            $total = $price * $qty;
            $result = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->update(['total' => $total]);
            $get_cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->first();

            if ($get_cart->qty < 1) {
                Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->delete();
                $get_cart = 'Cart deleted';
            }

            return response()->json([
                'success' => true,
                'data' => $get_cart
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'fail connect to table cart'
            ], 400);
        }
    }

    public function deleteCart(Request $req)
    {
        $cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->delete();
        if ($cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart delted'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No Cart'
            ], 400);
        }
    }


}
