<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ProductScheme;
use App\Models\User;
use Illuminate\Http\Request;

class ProductSchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req, $pg = null)
    {
        $offset = 0;
        $limit  = 4;
        $user = User::where('id', $req->user_id)->first();
        if ($user) {
            $scheme_id =  $user->scheme_id;
            $pro_price_scheme = ProductScheme::where('scheme_id', $scheme_id)->with('products')->get();

            if ($pg > 0) {
                $offset = ($pg - 1) * $limit;
            }
            $pg ? $data = ProductScheme::where('scheme_id', $scheme_id)->with('products')->offset($offset)->limit($limit)->orderBy('id', 'DESC')->get()
                : $data = ProductScheme::where('scheme_id', $scheme_id)->with('products')->orderBy('id', 'DESC')->get();
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
            for ($i = 0; $i < count($data); $i++) {
                $price[] = $data[$i]->products->price;
                $unit_price[] = $data[$i]->unit_price;
                $data[$i]->setAttribute('margin', round($price[$i] - $unit_price[$i], 2));
            }
            return response()->json([
                'success' => true,
                'data' => $data,
                'sum_page' => $sum_page,
                'total_page' => $total_page
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No user id'
            ], 404);
        }
    }

    public function productByCategory (Request $req) {
        $user = User::where('id', $req->user_id)->first();
        if ($user) {
            $scheme_id =  $user->scheme_id;
            $pro_price_scheme = ProductScheme::whereRelation('products', 'category_id', $req->category_id)->where('scheme_id', $scheme_id)->with('products')->with('products.category')->get();
            if (count($pro_price_scheme) == 0) {
                //get category name
                $category = Category::where('id', $req->category_id)->first();
                if ($category) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No products',
                        'category_name' => $category->name
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Category Id',
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $pro_price_scheme,
                    'category_name' => $pro_price_scheme[0]['products']['category']['name']
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No user'
            ], 400);
        }
    }

    public function search(Request $request, $pg = null)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user'
            ], 400);
        }
        $scheme_id = $user->scheme_id;
        $data_search = $request->name;
        $parts = preg_split('/\s+/', $data_search);
        $lenght = count($parts);
        $pg ? $data = product_price_scheme::query() : $data = product_price_scheme::query();
        for ($i = 0; $i < $lenght; $i++) {
            $data->whereRelation('products', 'name', 'like', "%{$parts[$i]}%");
        }
        $total_page = count($data->get());
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }
        $pg ? $get_data = $data->offset($offset)->limit($limit)->orderBy('updated_at', 'DESC')->with('products')->where('scheme_id', $scheme_id)->get()
            : $get_data = $data->with('products')->where('scheme_id', $scheme_id)->get();
        if (count($get_data) == 0) {
            $total_page = 0;
            $sum_page = 0;
            return response()->json([
                'success' => false,
                'message' => 'Products not found',
                'sum_page' => $sum_page,
                'total_page' => $total_page
            ], 200);
        }
        if (($total_page - $offset) < $limit || empty($pg)) {
            $sum_page = $total_page;
        } else {
            $sum_page = $total_page - ($total_page - $offset) + $limit;
        }
        return response()->json([
            'success' => true,
            'message' => 'Products found',
            'data' => $get_data,
            'sum_page' => $sum_page,
            'total_page' => $total_page
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $user = User::where('id', $req->user_id)->first();
        if ($user) {
            $scheme_id =  $user->scheme_id;
            $pro_price_scheme = ProductScheme::where('product_id', $req->product_id)->where('scheme_id', $scheme_id)->with('products')->with('products.category')->first();
            if ($pro_price_scheme == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product Not Found'
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $pro_price_scheme
                ], 200);
            }

//            if (!$pro_price_scheme) {
//                return response()->json([
//                    'success' => false,
//                    'data' => "Can't fetch data"
//                ], 400);
//            }
        } else {
            return response()->json([
                'success' => false,
                'data' => "Can't fetch data"
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
