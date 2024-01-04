<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSlideRequest;
use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
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


        $pg ? $slide = Slide::offset($offset)->limit($limit)->orderBy('slide_order', 'asc')->get() : $slide = Slide::orderBy('slide_order', 'asc')->get();
        $total_page = count(Slide::all());
        if (count($slide) == 0) {
            $total_page = 0;
            $sum_page = 0;
        }
        if (($total_page - $offset) < $limit || empty($pg)) {
            $sum_page = $total_page;
        } else {
            $sum_page = $total_page - ($total_page - $offset) + $limit;
        }
        if (!$slide) {
            return response()->json([
                'success' => false,
                'message' => 'Not found slide'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $slide,
            'per_page' => $limit,
            'sum_page' => $sum_page,
            'total_item' => $total_page,
            'total_page' => ceil($total_page / $limit)
        ], 200);
    }

    public function search (Request $request) {
        $data = Slide::query();
        if ($request->title != null) {
            $search = $request->title;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('title', 'like', "%{$parts[$i]}%");
            }
        }

        if ($request->tage != null) {
            $search = $request->tage;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('tage', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }

        if ($request->slide_order != null) {
            $search = $request->slide_order;
            $data->where('slide_order', $search);
        }


        if ($request->title == null && $request->slide_order == null && $request->tage == null) {
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $slide_order = Slide::max('slide_order');
        if ($slide_order == 0) {
            $slide_order  = 0;
        } else {
            $slide_order = $slide_order;
        }
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        if ($request->hasFile('image')) {
            $folder_name = 'uploads/slide/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

            if (!file_exists($folder_name)) {
                mkdir($folder_name, 0777, true);
            }
            $file = $request->file('image');

            $extention = strtolower($file->getClientOriginalExtension());
            $image_name = time() . rand() . "." . $extention;
            $uploads_path = $folder_name . "/";
            $image_url = "/" . $uploads_path . $image_name;
            $file->move($folder_name . '/', $image_name);
            $insert = new Slide;
            $insert->image = $image_url;
            $insert->slide_order  = $slide_order + 1;
            $insert->title  = $request->title;
            $insert->tage  = $request->tage;
            $insert->link  = $request->link;
            $insert->action  = $request->action;
            $insert_slide = $insert->save();
            $get_insert = $insert->refresh();
            if (!$insert_slide) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error '
                ], 400);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $get_insert
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'mesaage' => 'No Files image'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slide_id = null)
    {
        $get_slide = Slide::where('id', $slide_id)->first();
        if ($slide_id != null) {
            if ($get_slide) {
                return response()->json([
                    'success' => true,
                    'data' => $get_slide
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'slide not found'
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The ID must be no null.'
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
    public function update(UpdateSlideRequest $req, string $id = null)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        $get_slide = Slide::where('id', $id)->first();

        if ($get_slide) {

            $old_img = $get_slide->image;
            if ($req->new_image) {
                $folder_name = 'uploads/slide/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;
                if (!file_exists($folder_name)) {
                    mkdir($folder_name, 0777, true);
                }
                $file = $req->file('new_image');
                $extention = strtolower($file->getClientOriginalExtension());
                $image_name = time() . rand() . "." . $extention;
                $uploads_path = $folder_name . "/";
                $image_url = "/" . $uploads_path . $image_name;
                $file->move($folder_name . '/', $image_name);
                //delete old img in local
                $new_image =  $image_url;
                if (file_exists(substr($get_slide->image, 1))) { //check file's_path
                    unlink(substr($get_slide->image, 1));
                }
                //  unlink(substr($get_slide->image, 1));
            } else {
                $new_image = $get_slide->image;
            }

            if ($req->new_order) {
                $slide_order = Slide::max('slide_order');
                $where_slide_order = Slide::where('id', $req->id)->first();
                $old_slide_order = $where_slide_order->slide_order;
                $slide = Slide::all();
                if ($old_slide_order == $slide_order) {
                    if ($req->new_order > $old_slide_order) {
                        return response()->json([
                            'success' => false,
                            'message' => 'The new order is out of the maximum order'
                        ], 400);
                    }
                    if ($req->new_order == $old_slide_order) {

                        $new_Order = $old_slide_order;
                    } else {
                        Slide::where('id', $id)->update(['slide_order' => $req->new_order]);
                        // return $old_slide_order;
                        Slide::where('id', '!=', $id)->where('slide_order', $req->new_order)->update(['slide_order' => $old_slide_order]);
                    }
                } else {
                    if ($req->new_order > $slide_order) {
                        return response()->json([
                            'success' => false,
                            'message' => 'The new order is out of the maximum order'
                        ], 400);
                    }
                    if ($req->new_order == $slide_order) {

                        $new_Order = $slide_order;

                        Slide::where('id', $id)->update(['slide_order' => $new_Order]);
                        // return $old_slide_order;
                        Slide::where('id', '!=', $id)->where('slide_order', $slide_order)->update(['slide_order' => $old_slide_order]);
                    } else {
                        Slide::where('id', $id)->update(['slide_order' => $req->new_order]);
                        // return $old_slide_order;
                        Slide::where('id', '!=', $id)->where('slide_order', $req->new_order)->update(['slide_order' => $old_slide_order]);
                    }
                }
                $get_slide = Slide::where('id', $id)->first();
                $new_order = $get_slide->slide_order;
            } else {
                $new_order = $get_slide->slide_order;
            }
            if ($req->new_title) {
                $new_title = $req->new_title;
            } else {
                $new_title = $get_slide->title;
            }
            if ($req->new_tage) {
                $new_tage = $req->new_tage;
            } else {
                $new_tage = $get_slide->tage;
            }
            if ($req->new_link) {
                $new_link = $req->new_link;
            } else {
                $new_link = $get_slide->link;
            }
            if ($req->action != null) {
                $action = $req->action;
            } else {
                $action = $get_slide->action;
            }

            $update_slide = Slide::where('id', $id)->update(['image' => $new_image, 'slide_order' => $new_order, 'title' => $new_title, 'tage' => $new_tage, 'link' => $new_link, 'action' => $action]);
            return response()->json([
                'success' => true,
                'meesage' => 'The slide has been updated'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'The slide ID Not found'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $deleted = Slide::where('id', $request->id)->delete();
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'No slide'
            ], 400);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'You has been deleted the slide'
            ], 200);
        }
    }
}
