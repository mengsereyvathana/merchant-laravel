<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductScheme;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($pg = null)
    {
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }
        $pg ? $data = Product::offset($offset)->limit($limit)->orderBy('id', 'DESC')->with('category')->get()
            : $data = Product::orderBy('id', 'DESC')->with('category')->get();
        $total_page = count(Product::all());

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
            'total_item' => $total_page,
            'total_page' => ceil($total_page / $limit),
            'sum_page' => $sum_page,
        ], 200);
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
        $pg ? $data = ProductScheme::query() : $data = ProductScheme::query();
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
    public function show(string $id = null)
    {
        $product = Product::with('category')->find($id);
        if ($id != null) {
            if ($product) {
                return response()->json([
                    'success' => true,
                    'data' => $product
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'please input the products Id'
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
