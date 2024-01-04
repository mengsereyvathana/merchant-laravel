<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

        $pg ? $get = Category::offset($offset)->limit($limit)->get() : $get = Category::all();
        $total_page = count(Category::all());
        if (count($get) == 0) {
            $total_page = 0;
            $sum_page = 0;
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
                'message' => 'No category data'
            ], 400);
        }
    }

    public function search (Request $request) {
        $data = Category::query();
        if ($request->name != null) {
            $search = $request->name;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('name', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }
        if ($request->name == null) {
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        if ($request->hasFile('image')) {
            $folder_name = 'uploads/category/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

            if (!file_exists($folder_name)) {
                mkdir($folder_name, 0777, true);
            }
            $file = $request->file('image');
            $extention = strtolower($file->getClientOriginalExtension());
            $image_name = time() . rand() . "." . $extention;
            $uploads_path = $folder_name . "/";
            $image_url = "/" . $uploads_path . $image_name;
            $file->move($folder_name . '/', $image_name);
            $data = new Category;
            $data->name = $request->name;
            $data->description = $request->des;
            $data->image = $image_url;
            $data->action = $request->action;
            $insert = $data->save();
            $get_data = $data->refresh();
            if ($insert) {
                return response()->json([
                    'success' => true,
                    'data' => $get_data
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $get_data
                ], 200);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id = null)
    {
        if ($id == null) {
            return response()->json([
                'success' => false,
                'mesage' => 'The ID must not be null.'
            ], 400);
        } else {
            $data = Category::where('id', $id)->first();
            if ($data) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'mesage' => 'The ID not found.'
                ], 200);
            }
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
        if ($id != null) {
            $check = Category::where('id', $id)->first();
            if ($check) {
                $currentYear = date('Y');
                $currentMonth = date('m');
                $currentDay = date('d');
                if ($request->hasFile('image')) {
                    $folder_name = 'uploads/category/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

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

                $old_category = Category::where('id', $id)->first();
                if ($request->name != null) {
                    $new_name = $request->name;
                } else {
                    $new_name = $old_category->name;
                }
                if (!$request->image != null) {
                    $new_image = $old_category->image;
                } else {
                    if (file_exists(substr($old_category->image, 1))) { //check file's_path
                        unlink(substr($old_category->image, 1));
                    }
                    $new_image = $image_url;
                }
                if ($request->des != null) {
                    $new_des = $request->des;
                } else {
                    $new_des = $old_category->description;
                }
                if ($request->action != null) {
                    $new_action = $request->action;
                } else {
                    $new_action = $old_category->action;
                }
                $update = Category::where('id', $id)->update(['name' => $new_name, 'image' => $new_image, 'description' => $new_des, 'action' => $new_action]);
                if ($update) {
                    return response()->json([
                        'success' => true,
                        'message' => 'The product has been updated',
                    ], 201);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'The process has an error'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No products'
                ], 401);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The ID must not be null.'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id = null)
    {
        if ($id !== null) {
            $data = Category::where('id', $id)->delete();
            if ($data) {
                return response()->json([
                    'success' => true,
                    'mesage' => 'The Category has been deleted.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'mesage' => 'The process has an error.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'mesage' => 'The ID must not be null.'
            ], 200);
        }
    }
}
