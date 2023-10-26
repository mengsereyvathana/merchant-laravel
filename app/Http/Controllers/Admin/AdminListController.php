<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Ht;
use Illuminate\Http\Exception;
use App\Models\products;
use App\Models\category;
use App\Models\User;
use App\Models\Cart;
use App\Models\order;
use App\Models\orderDetail;
use App\Models\scheme;
use App\Models\slide;
use App\Models\product_price_scheme;
use App\Http\Requests\AddListRequest;
use App\Http\Requests\ProductSchemePriceRequest;
use App\Http\Requests\addCartRequest;
use App\Http\Requests\UpdateListRequest;
use App\Http\Requests\UpdateSlideRequest;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;

class AdminListController extends Controller
{

    public function list(Request $req)
    {
        $pg = $req->query('page');

        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }
        $pg ? $data = products::offset($offset)->limit($limit)->orderBy('id', 'DESC')->with('category')->get()
            : $data = products::orderBy('id', 'DESC')->with('category')->get();
        $total_page = count(products::all());

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

    public function add_list(AddListRequest $request)
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

            $data = products::create([
                'category_id' => $request->category_id,
                'name'       => $request->name,

                'price'      => round($request->price, 4),
                'image'      => $image_url,
                //    'image'      => 'dd.jpg',
                'color'      => $request->color,
                'description' => $request->description,
                'ram'        => $request->ram,
                'storage'    => $request->storage,
                'buy'        => round($request->buy, 4),
                'margin'     => round($request->price - $request->buy, 4),
                'stock'      => $request->stock,

                'action'     => $request->action
            ]);
            // return $request->categoryid;
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

    public function update_list(UpdateListRequest $req, $id)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        if ($req->hasFile('image')) {
            $folder_name = 'uploads/product/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

            if (!file_exists($folder_name)) {
                mkdir($folder_name, 0777, true);
            }
            $file = $req->file('image');
            $extention = strtolower($file->getClientOriginalExtension());
            $image_name = time() . rand() . "." . $extention;
            $uploads_path = $folder_name . "/";
            $image_url = "/" . $uploads_path . $image_name;
            $file->move($folder_name . '/', $image_name);
        }
        $update = products::find($id);
        // return substr($update->image, 1);

        if ($update) {
            if ($req->category_id != null) {
                $update->category_id     = $req->category_id;
            } else {
                $update->category_id = $update->category_id;
            }
            if ($req->name != null) {
                $update->name       = $req->name;
            } else {
                $update->name = $update->name;
            }
            if ($req->price != null) {
                $update->price      = $req->price;
            } else {
                $update->price = $update->price;
            }
            if ($req->image != null) {
                if (file_exists(substr($update->image, 1))) { //check file's_path
                    unlink(substr($update->image, 1));
                }
                $update->image      = $image_url;
            } else {
                $update->image = $update->image;
            }

            if ($req->color != null) {
                $update->color      = $req->color;
            } else {
                $update->color = $update->color;
            }
            if ($req->description != null) {
                $update->description = $req->description;
            } else {
                $update->description = $update->description;
            }
            if ($req->ram != null) {
                $update->ram        = $req->ram;
            } else {
                $update->ram = $update->ram;
            }
            if ($req->storage != null) {
                $update->storage    = $req->storage;
            } else {
                $update->storage = $update->storage;
            }
            if ($req->buy != null) {
                $update->buy        = $req->buy;
            } else {
                $update->buy = $update->buy;
            }
            if ($req->stock != null) {
                $update->stock      = $req->stock;
            } else {
                $update->stock = $update->stock;
            }
            if ($req->action != null) {
                $update->action     = $req->action;
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

    public function delete($id = null)
    {
        if (empty($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Products id not found'
            ], 404);
            // return 'g';
        }
        $article = products::find($id);
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

    public function user_scheme_price_list(Request $req)
    {
        $offset = 0;
        $limit  = 4;
        $pg = $req->query('page');
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
            ], 200);
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

    public function list_ordered(Request $req)
    {
        $pg = $req->query('page');
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }

        $pg ? $order_detail = orderDetail::whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice')->skip($offset)->take($limit) :
            $order_detail = orderDetail::whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice');
        $total_invoice = count(orderDetail::whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice'));
        if (($total_invoice - $offset) < $limit || empty($pg)) {
            $sum_page = $total_invoice;
        } else {
            $sum_page = $total_invoice - ($total_invoice - $offset) + $limit;
        }
        $total_page = ceil($total_invoice / $limit);
        if ($pg > $total_page) {
            return response()->json([
                'success' => true,
                'message' => 'No Products',
                'data' => [],
                'per_page' => $limit,
                'sum_page' => 0,
                'total_item' => 0,
                'total_page' => 0
            ], 200);
        }
        if (count($order_detail) == 0) {
            return response()->json([
                'success' => true,
                'message' => 'No Products',
                'per_page' => $limit,
                'sum_page' => 0,
                'total_item' => 0,
                'total_page' => 0
            ], 200);
        } else {

            $collections = collect($order_detail);

            $groupedInvoices = $collections->toArray();
            // return  $groupedInvoices[3];
            $myKeys = array_keys($groupedInvoices);
            // return $groupedInvoices;
            for ($a = 0; $a < count($groupedInvoices); $a++) {
                $invoice[] = $groupedInvoices[$myKeys[$a]][0]['order']['invoice'];
            }
            // return $invoice;
            // but have the same invocie so, convert by unique 100(3)&2345(3) =>{"0":100,"3":2345}
            $collection = collect($invoice);

            //  return $collection;
            $uniqueincoive = $collection->unique();
            $date =  orderDetail::query();
            //loop 0-1 
            for ($i = 0; $i < count($uniqueincoive); $i++) {
                // find value of invocie (100&2345) type obj =>{"0":100,"3":2345}
                $collection = collect($uniqueincoive);

                // covert obj type to array type =>{"0":100,"3":2345}
                $arrays = $collection->toArray();


                // covert key of obj to array =>[0,3]
                $keys = array_keys($arrays);

                // get value of obj point by key cus $keys[0]=100,$keys[1]=2345 =>$arrays[0]&$array[3] =>getInvoice [100,2345]
                $getInvoice[] = $arrays[$keys[$i]];
                $date->orwhereRelation('order', 'invoice', $arrays[$keys[$i]]);
                // groupInvoice have key 100 & 2345 of 6 data so, we poit key of group and insert into array 
                $get_group[] = $order_detail[$getInvoice[$i]];

                $total[] = array_sum(array_column($groupedInvoices[$getInvoice[$i]], 'total'));
            }
            //    return $arrays;
            $getInvoice_ordered = implode(',', $getInvoice);
            $date = order::whereIn('invoice', $getInvoice)->orderByRaw("FIELD(invoice, $getInvoice_ordered)")->with('user')->get();

            for ($i = 0; $i < count($getInvoice); $i++) {
                $dates[] = $date[$i]['created_at'];
                $buy_by[] = $date[$i]['user'];
                $buy_by_name[] =  $buy_by[$i]['name'];
            }
            $invoice_date = $dates;
            for ($i = 0; $i < count($getInvoice); $i++) {
                $invoice_date[$i] = date('d-m-Y', strtotime($invoice_date[$i]));
            }
            return response()->json([
                'success' => true,
                'data' => $get_group,
                'per_page' => $limit,
                'sum_page' => $sum_page,
                'total_invoice' => $total_invoice,
                'total_page' => ceil($total_invoice / $limit),
                'invoice' => $getInvoice,
                'invoice_date' => $invoice_date,
                'buy_by' => $buy_by_name,
                'total' => $total,
            ], 200);
        }
    }


    public function List_ordered_delete($invoice = null)
    {
        if ($invoice == null) {
            return response()->json([
                'success' => false,
                'message' => 'The ID must not be null.'
            ], 400);
        } else {
            $ordered =  orderDetail::whereRelation('order', 'invoice', $invoice)->delete();
            if ($ordered) {
                return response()->json([
                    'success' => true,
                    'message' => 'The Invoice has been deleted.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The process has an error.'
                ], 400);
            }
        }
    }

    public function list_category(Request $req)
    {
        $pg = $req->query('page');
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }

        $pg ? $get = category::offset($offset)->limit($limit)->get() : $get = category::all();
        $total_page = count(category::all());
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

    public function add_category(Request $request)
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
            $data = new category;
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

    public function delete_category($id = null)
    {
        if ($id !== null) {
            $data = category::where('id', $id)->delete();
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

    public function detail_category($id = null)
    {
        if ($id == null) {
            return response()->json([
                'success' => false,
                'mesage' => 'The ID must not be null.'
            ], 400);
        } else {
            $data = category::where('id', $id)->first();
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

    public function update_category(Request $req, $id = null)
    {
        if ($id != null) {
            $check = category::where('id', $id)->first();
            if ($check) {
                $currentYear = date('Y');
                $currentMonth = date('m');
                $currentDay = date('d');
                if ($req->hasFile('image')) {
                    $folder_name = 'uploads/category/' . $currentYear . '/' . $currentMonth . '/' . $currentDay;

                    if (!file_exists($folder_name)) {
                        mkdir($folder_name, 0777, true);
                    }
                    $file = $req->file('image');
                    $extention = strtolower($file->getClientOriginalExtension());
                    $image_name = time() . rand() . "." . $extention;
                    $uploads_path = $folder_name . "/";
                    $image_url = "/" . $uploads_path . $image_name;
                    $file->move($folder_name . '/', $image_name);
                }

                $old_category = category::where('id', $id)->first();
                if ($req->name != null) {
                    $new_name = $req->name;
                } else {
                    $new_name = $old_category->name;
                }
                if (!$req->image != null) {
                    $new_image = $old_category->image;
                } else {
                    if (file_exists(substr($old_category->image, 1))) { //check file's_path
                        unlink(substr($old_category->image, 1));
                    }
                    $new_image = $image_url;
                }
                if ($req->des != null) {
                    $new_des = $req->des;
                } else {
                    $new_des = $old_category->description;
                }
                if ($req->action != null) {
                    $new_action = $req->action;
                } else {
                    $new_action = $old_category->action;
                }
                $update = category::where('id', $id)->update(['name' => $new_name, 'image' => $new_image, 'description' => $new_des, 'action' => $new_action]);
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

    public function add_slide(Request $request)
    {
        $slide_order = slide::max('slide_order');
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
            $insert = new slide;
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

    public function list_slide(Request $req)
    {
        $pg = $req->query('page');
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }


        $pg ? $slide = slide::offset($offset)->limit($limit)->orderBy('slide_order', 'asc')->get() : $slide = slide::orderBy('slide_order', 'asc')->get();
        $total_page = count(slide::all());
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

    public function delete_slide(Request $req)
    {
        $deleted = slide::where('id', $req->id)->delete();
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

    public function detail_slide($slide_id = null)
    {
        $get_slide = slide::where('id', $slide_id)->first();
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

    public function update_slide(UpdateSlideRequest $req, $id = null)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        //find slide 
        $get_slide = slide::where('id', $id)->first();

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
                $slide_order = slide::max('slide_order');
                $where_slide_order = slide::where('id', $req->id)->first();
                $old_slide_order = $where_slide_order->slide_order;
                $slide = slide::all();
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
                        slide::where('id', $id)->update(['slide_order' => $req->new_order]);
                        // return $old_slide_order;
                        slide::where('id', '!=', $id)->where('slide_order', $req->new_order)->update(['slide_order' => $old_slide_order]);
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
                        // return $new_Order;


                        slide::where('id', $id)->update(['slide_order' => $new_Order]);
                        // return $old_slide_order;
                        slide::where('id', '!=', $id)->where('slide_order', $slide_order)->update(['slide_order' => $old_slide_order]);
                    } else {
                        slide::where('id', $id)->update(['slide_order' => $req->new_order]);
                        // return $old_slide_order;
                        slide::where('id', '!=', $id)->where('slide_order', $req->new_order)->update(['slide_order' => $old_slide_order]);
                    }
                }
                $get_slide = slide::where('id', $id)->first();
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

            $update_slide = slide::where('id', $id)->update(['image' => $new_image, 'slide_order' => $new_order, 'title' => $new_title, 'tage' => $new_tage, 'link' => $new_link, 'action' => $action]);
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

    public function search_product(Request $req)
    {
        $data = products::query();
        if ($req->name != null) {
            $search = $req->name;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('name', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }
        if ($req->id != null) {
            $search = $req->id;
            $data->where('id', $search);
        }


        if ($req->name == null && $req->id == null) {
            return response()->json([
                "success" => false,
                "message" => "The input file must not be null.",
            ], 200);
        }

        $total_page = count($data->get());

        // paginatino
        $pg = $req->query('page');
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

    public function search_scheme(Request $req)
    {
        $data = product_price_scheme::query();
        if ($req->name != null) {
            $search = $req->name;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->whereRelation('products', 'name', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }
        if ($req->scheme_id != null) {
            $search = $req->scheme_id;
            $data->where('scheme_id', $search);
        }

        if ($req->product_id != null) {
            $search = $req->product_id;
            $data->where('product_id', $search);
        }

        if ($req->name == null && $req->scheme_id == null && $req->product_id == null) {
            return response()->json([
                "success" => false,
                "message" => "The input file must not be null.",
            ], 200);
        }

        $total_page = count($data->get());

        // paginatino
        $pg = $req->query('page');
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

    public function search_slide(Request $req)
    {
        $data = slide::query();
        if ($req->title != null) {
            $search = $req->title;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('title', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }

        if ($req->tage != null) {
            $search = $req->tage;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('tage', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }

        if ($req->slide_order != null) {
            $search = $req->slide_order;
            $data->where('slide_order', $search);
        }


        if ($req->title == null && $req->slide_order == null && $req->tage == null) {
            return response()->json([
                "success" => false,
                "message" => "The input file must not be null.",
            ], 200);
        }

        $total_page = count($data->get());

        // paginatino
        $pg = $req->query('page');
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

    public function search_order(Request $req)
    {
        $order_detail = orderDetail::whereRelation('order', 'invoice', $req->invoice)->whereRelation('order', 'status', 'delivered')->with('order', 'product')->get();
        $total_invoice = count(orderDetail::whereRelation('order', 'invoice', $req->invoice)->whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice'));
        // $order_date = $order_detail[0]['order']['updated_at'];
        // if($order_detail){

        if (count($order_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'No Products',
            ], 200);
        } else {
            //make group by invoice
            $groupedInvoice = $order_detail->groupBy('order.invoice');
            $collections = collect($groupedInvoice);
            // covert obj type to array type =>{"0":100,"3":2345}
            $groupedInvoices = $collections->toArray();


            //get all invoice value in data total 6 invocie
            for ($a = 0; $a < count($order_detail); $a++) {
                $invoice[] = $order_detail[$a]['order']['invoice'];
            }

            // but have the same invocie so, convert by unique 100(3)&2345(3) =>{"0":100,"3":2345}
            $collection = collect($invoice);

            //  return $collection;
            $uniqueincoive = $collection->unique();
            $date =  orderDetail::query();
            // return $uniqueincoive;
            //loop 0-1 
            for ($i = 0; $i < count($uniqueincoive); $i++) {
                // find value of invocie (100&2345) type obj =>{"0":100,"3":2345}
                $collection = collect($uniqueincoive);

                // covert obj type to array type =>{"0":100,"3":2345}
                $arrays = $collection->toArray();

                // covert key of obj to array =>[0,3]
                $keys = array_keys($arrays);

                // get value of obj point by key cus $keys[0]=100,$keys[1]=2345 =>$arrays[0]&$array[3] =>getInvoice [100,2345]
                $getInvoice[] = $arrays[$keys[$i]];
                $date->orwhereRelation('order', 'invoice', $arrays[$keys[$i]]);
                // groupInvoice have key 100 & 2345 of 6 data so, we poit key of group and insert into array 
                // =>get_group [[],[]]
                $get_group[] = $groupedInvoice[$getInvoice[$i]];

                $total[] = array_sum(array_column($groupedInvoices[$getInvoice[$i]], 'total'));
            }

            // return $date->get();
            return response()->json([
                'success' => true,
                'data' => $get_group,
                'invoice' => $getInvoice,
                // 'invoice_date'=>$getInvoice_date,
                'total' => $total,
            ], 200);
        }
        // }
    }
    public function search_category(Request $req)
    {
        $data = category::query();
        if ($req->name != null) {
            $search = $req->name;
            $parts = preg_split('/\s+/', $search);
            $lenght = count($parts);
            for ($i = 0; $i < $lenght; $i++) {
                $data->orwhere('name', 'like', "%{$parts[$i]}%");
            }
            // $data->where('name', 'like' , "%{$search}%");
        }
        if ($req->name == null) {
            return response()->json([
                "success" => false,
                "message" => "The input file must not be null.",
            ], 200);
        }

        $total_page = count($data->get());

        // paginatino
        $pg = $req->query('page');
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
}
