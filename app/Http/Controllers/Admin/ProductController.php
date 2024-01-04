<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductScheme;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pg = $request->query('page');

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
        $minutesToAdd = 1;
        $newTime = strtotime("+$minutesToAdd minutes");
        date_default_timezone_set('Asia/Bangkok');
        $date = date('i:s', $newTime);
        return response()->json([
            'date' => $date,
            'success' => true,
            'data' => $data,
            'per_page' => $limit,
            'sum_page' => $sum_page,
            'total_item' => $total_page,
            'total_page' => ceil($total_page / $limit)
        ], 200);
    }

    public function search (Request $request) {
        $data = Product::query();
        if ($request->name != null) {
            $search = $request->name;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('name', 'like', "%{$parts[$i]}%");
            }
        }
        if ($request->id != null) {
            $search = $request->id;
            $data->where('id', $search);
        }


        if ($request->name == null && $request->id == null) {
            return response()->json([
                "success" => false,
                "message" => "The input file must not be null.",
            ], 200);
        }

        $total_page = count($data->get());

        $pg = $request->query('page');
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }

        $pg ? $get = $data->offset($offset)->limit($limit)->get() : $get =  $data->get();

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

    public function productByCategory(Request $request) {
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $scheme_id =  $user->scheme_id;
            $pro_price_scheme = ProductScheme::whereRelation('products', 'category_id', $request->category_id)->where('scheme_id', $scheme_id)->with('products')->with('products.category')->get();
            if (count($pro_price_scheme) == 0) {
                //get category name
                $category = Category::where('id', $request->category_id)->first();
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
    public function store(CreateProductRequest $request)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        if ($request->hasFile('image')) {
            $folder_name = 'uploads/product/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

            if (!file_exists($folder_name)) {
                mkdir($folder_name, 0777, true);
            }

            $file = $request->file('image');
            $extention = strtolower($file->getClientOriginalExtension());
            $image_name = time() . rand() . "." . $extention;
            $uploads_path = $folder_name . "/";
            $image_url = "/" . $uploads_path . $image_name;
            $file->move($folder_name . '/', $image_name);

            $data = Product::create([
                'category_id' => $request->category_id,
                'name'       => $request->name,
                'price'      => round($request->price, 4),
                'image'      => $image_url,
                'color'      => $request->color,
                'description' => $request->description,
                'ram'        => $request->ram,
                'storage'    => $request->storage,
                'buy'        => round($request->buy, 4),
                'margin'     => round($request->price - $request->buy, 4),
                'stock'      => $request->stock,

                'action'     => $request->action
            ]);
            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => 'The products has been uploaded.',
                    'data'    => $data
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload has been errors.',
                ], 401);
            }
        }
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
    public function update(UpdateProductRequest $request, string $id)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        if ($request->hasFile('image')) {
            $folder_name = 'uploads/product/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

            if (!file_exists($folder_name)) {
                mkdir($folder_name, 0777, true);
            }
            $file = $request->file('image');
            $extention = strtolower($file->getClientOriginalExtension());
            $image_name = time() . rand() . "." . $extention;
            $uploads_path = $folder_name . "/";
            $image_url = "/" . $uploads_path . $image_name;
            $file->move($folder_name . '/', $image_name);
        }
        $update = Product::find($id);
        // return substr($update->image, 1);

        if ($update) {
            if ($request->category_id != null) {
                $update->category_id     = $request->category_id;
            } else {
                $update->category_id = $update->category_id;
            }
            if ($request->name != null) {
                $update->name       = $request->name;
            } else {
                $update->name = $update->name;
            }
            if ($request->price != null) {
                $update->price      = $request->price;
            } else {
                $update->price = $update->price;
            }
            if ($request->image != null) {
                if (file_exists(substr($update->image, 1))) { //check file's_path
                    unlink(substr($update->image, 1));
                }
                $update->image      = $image_url;
            } else {
                $update->image = $update->image;
            }

            if ($request->color != null) {
                $update->color      = $request->color;
            } else {
                $update->color = $update->color;
            }
            if ($request->description != null) {
                $update->description = $request->description;
            } else {
                $update->description = $update->description;
            }
            if ($request->ram != null) {
                $update->ram        = $request->ram;
            } else {
                $update->ram = $update->ram;
            }
            if ($request->storage != null) {
                $update->storage    = $request->storage;
            } else {
                $update->storage = $update->storage;
            }
            if ($request->buy != null) {
                $update->buy        = $request->buy;
            } else {
                $update->buy = $update->buy;
            }
            if ($request->stock != null) {
                $update->stock      = $request->stock;
            } else {
                $update->stock = $update->stock;
            }
            if ($request->action != null) {
                $update->action     = $request->action;
            } else {
                $update->action = $update->action;
            }
            // return $update->name;

            $update->created_at = $update->created_at;
            $update->updated_at =  now();

            $result = $update->save();
            $data   = $update->refresh();
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'You have benn updated',
                    'data'    => $data
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The process has an error.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No products'
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (empty($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Products id not found'
            ], 404);
            // return 'g';
        }
        $article = Product::find($id);
        if ($article) {
            $article->delete();
            return response()->json([
                'success' => true,
                'message' => 'You have been deleted the product.'

            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'meassage'  => 'No products'
            ], 404);
        }
    }
}
