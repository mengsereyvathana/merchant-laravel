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
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $carts = Cart::where('user_id', $userId)->with('products')->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found',
            ], 200);
        }

        $amount = $carts->sum('total');
        $totalQty = $carts->sum('qty');

        return response()->json([
            'success' => true,
            'data' => $carts,
            'amount' => $amount,
            'amount_qty' => $totalQty,
        ], 200);
    }

    public function addToCart(AddToCartRequest $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user',
            ], 400);
        }

        $scheme_id = $user->scheme_id;
        $product_price_scheme = ProductScheme::where('scheme_id', $scheme_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$product_price_scheme) {
            return response()->json([
                'success' => false,
                'message' => 'No product',
            ], 400);
        }

        $scheme_price = $product_price_scheme->unit_price;

        $cart = Cart::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cart) {
            $cart = new Cart;
            $cart->user_id = $request->user_id;
            $cart->product_id = $request->product_id;
            $cart->qty = 1;
            $cart->unit_price = $scheme_price;
            $cart->total = $scheme_price;
            $cart->save();
        } else {
            $cart->increment('qty');
            $cart->total = $cart->qty * $scheme_price;
            $cart->save();
        }

        return response()->json([
            'success' => true,
            'data' => $cart,
        ], 200);
    }

    public function subtractFromCart(AddToCartRequest $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user',
            ], 400);
        }

        $scheme_id = $user->scheme_id;
        $product_price_scheme = ProductScheme::where('scheme_id', $scheme_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$product_price_scheme) {
            return response()->json([
                'success' => false,
                'message' => 'No product',
            ], 400);
        }

        $scheme_price = $product_price_scheme->unit_price;

        $cart = Cart::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->decrement('qty');
            $cart->total = $cart->qty * $scheme_price;
            $cart->save();

            if ($cart->qty < 1) {
                $cart->delete();
                $message = 'Cart deleted';
            } else {
                $message = '';
            }

            return response()->json([
                'success' => true,
                'data' => $cart,
                'message' => $message,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to connect to table cart',
        ], 400);
    }

    public function deleteCart(Request $request)
    {
        $cart = Cart::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->delete();

        if ($cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart deleted',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No Cart',
        ], 400);
    }
}
