<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSchemeRequest;
use App\Models\Product;
use App\Models\ProductScheme;
use Illuminate\Http\Request;

class ProductSchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $offset = 0;
        $limit  = 4;
        $pg = $request->query('page');
        $tbl = ProductScheme::all();
        //check user id
        if ($tbl) {
            //   $scheme_id =  $user->scheme_id;
            $pro_price_scheme = ProductScheme::with('products')->get();

            if ($pg > 0) {
                $offset = ($pg - 1) * $limit;
            }
            $pg ? $data = ProductScheme::with('products')->offset($offset)->limit($limit)->orderBy('id', 'DESC')->get()
                : $data = ProductScheme::with('products')->with('products.category')->orderBy('id', 'DESC')->get();
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
                'total_item' => $total_page,
                'total_page' => ceil($total_page / $limit)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No Table product_price_scheme'
            ], 404);
        }
    }

    public function search (Request $request) {
        $data = ProductScheme::query();
        if ($request->name != null) {
            $search = $request->name;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->whereRelation('products', 'name', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }
        if ($request->scheme_id != null) {
            $search = $request->scheme_id;
            $data->where('scheme_id', $search);
        }

        if ($request->product_id != null) {
            $search = $request->product_id;
            $data->where('product_id', $search);
        }

        if ($request->name == null && $request->scheme_id == null && $request->product_id == null) {
            return response()->json([
                "success" => false,
                "message" => "The input file must not be null.",
            ], 200);
        }

        $total_page = count($data->get());

        // paginatino
        $pg = $request->query('page');
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }

        $pg ? $get = $data->offset($offset)->limit($limit)->orderBy('updated_at', 'DESC')->with('products')->get() : $get =  $data->orderBy('updated_at', 'DESC')->with('products')->get();

        if (count($get) == 0) {
            return response()->json([
                'success' => true,
                'message' => 'The product not fonud',
                'per_page' => 0,
                'sum_page' => 0,
                'total_item' => 0,
                'total_page' => 0
            ], 200);
        }
        if (($total_page - $offset) < $limit || empty($pg)) {
            $sum_page = $total_page;
        } else {
            $sum_page = $total_page - ($total_page - $offset) + $limit;
        }
        if ($get) {
            return response()->json([
                'success' => true,
                'data' => $get,
                'per_page' => $limit,
                'sum_page' => $sum_page,
                'total_item' => $total_page,
                'total_page' => ceil($total_page / $limit)
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'The process has an error.'
            ], 400);
        }
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
    public function store(ProductSchemeRequest $request)
    {
        $pro_id = $request->pro_id;
        $scheme_id = $request->scheme_id;
        $unit_price = $request->unit_price;
        $check = ProductScheme::where('scheme_id', $scheme_id)->where('product_id', $pro_id)->first();
        if ($check) {
            return response()->json([
                'success' => false,
                'message' => 'This product scheme price has already been created.',
            ], 200);
        } else {
            $get_product = Product::where('id', $pro_id)->first();
            if ($get_product) {
                $price = $get_product->price;
                $margin = $price - $unit_price;
                $margin;
                $insert_pro_scheme_price = new ProductScheme;
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
                    ], 201);
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
                ], 200);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id = null)
    {
        if ($id != null) {
            $check = ProductScheme::where('id', $id)->first();
            if ($check) {
                $de_product = ProductScheme::where('id', $id)->with('products')->first();
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
    public function update(Request $request, string $id = null)
    {
        $old_product = ProductScheme::where('id', $id)->first();
        $product_id = $old_product->product_id;
        $product = Product::where('id', $product_id)->first();
        $product_price = $product->price;
        if ($old_product) {
            if ($request->product_id == null) {
                $new_product_id = $old_product->product_id;
            } else {
                $new_product_id = $request->product_id;
            }
            if ($request->scheme_id == null) {
                $new_scheme_id = $old_product->scheme_id;
            } else {
                $new_scheme_id = $request->scheme_id;
            }
            if ($request->action == null) {
                $new_action = $old_product->action;
            } else {
                $new_action = $request->action;
            }
            if ($request->unit_price == null) {
                $new_unit_price =  $old_product->unit_price;
                $margin = $old_product->margin;
            } else {
                $new_unit_price =  $request->unit_price;
                $margin = $product_price - $request->unit_price;
            }


            if ($request->product_id != null && $request->scheme_id != null) {
                if ($request->product_id != $old_product->product_id || $request->scheme_id != $old_product->scheme_id) {
                    $check_update = ProductScheme::where([['product_id', $new_product_id], ['scheme_id', $new_scheme_id]])->first();
                } else {
                    $check_update = false;
                }
            } else {
                $check_update = false;
            }
            if ($request->product_id != null &&  $request->scheme_id == null) {
                if ($request->product_id != $old_product->product_id) {
                    $check_update = ProductScheme::where([['product_id', $new_product_id], ['scheme_id', $new_scheme_id]])->first();
                } else {
                    $check_update = false;
                }
            }
            if ($request->product_id == null &&  $request->scheme_id != null) {

                if ($request->scheme_id != $old_product->scheme_id) {
                    $check_update = ProductScheme::where([['product_id', $new_product_id], ['scheme_id', $new_scheme_id]])->first();
                } else {
                    $check_update = false;
                }
            }

            if (!$check_update) {
                $update =  ProductScheme::where('id', $id)->update(['product_id' => $new_product_id, 'scheme_id' => $new_scheme_id, 'unit_price' => $new_unit_price, 'action' => $new_action, 'margin' => $margin]);
                $data =  ProductScheme::where('id', $id)->first();
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id = null)
    {
        if ($id != null) {
            $check = ProductScheme::where('id', $id)->first();
            if ($check) {
                $del_product = ProductScheme::where('id', $id)->delete();
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
