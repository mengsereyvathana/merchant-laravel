<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Collection;

class ListController extends Controller
{

    public function list($pg=null){
        $offset = 0;
        $limit  = 4;
        if($pg>0){            
            $offset=($pg-1)* $limit;
        }
         $pg?$data=products::offset($offset)->limit($limit)->orderBy('id', 'DESC')->/* with('category')-> */get()
         :$data=products::orderBy('id', 'DESC')->/* with('category')-> */get();
         $total_page=count(products::all());

        if(count($data)==0){
            $total_page=0;
            $sum_page=0;
        }
        if(($total_page-$offset)<$limit || empty($pg)){
            $sum_page = $total_page;
        }else{
            $sum_page = $total_page-($total_page-$offset)+$limit;
        }
        $minutesToAdd = 1;
        $newTime = strtotime("+$minutesToAdd minutes");
        date_default_timezone_set('Asia/Bangkok');
        $date= date('i:s',$newTime);
         return response()->json([
            'date'=>$date,
            'success'=>true,
            'data'=>$data,
            
            'sum_page'=>$sum_page,
            'total_page'=>$total_page
         ],200);
    }

    public function add_list(AddListRequest $request){
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
           $data = new products;
               $data->name       = $request->name;
               $data->price      = round($request->price,4);
               $data->image      = $image_url;
               $data->category_id= $request->category_id;
               $data->color      = $request->color;
               $data->description= $request->description;
               $data->ram        = $request->ram;
               $data->storage    = $request->storage;
               $data->buy        = round($request->buy,4);
               $data->margin     = round($request->price-$request->buy,4);
               $data->stock      = $request->stock;
               $data->action     = $request->action;
               $get_data = $data->save();
               $load_data = $data->refresh();
           if($get_data){
            return response()->json([
                'success' => true,
                'message' => 'The products has been uploaded.',
                'data'    => $load_data
               ],201);
           }else{
            return response()->json([
                'success' => false,
                'message' => 'Upload has been errors.',
               ],401);
           }
          
       }
    }

    public function update_list(UpdateListRequest $req,$id){
            $currentYear = date('Y');
            $currentMonth = date('m');
            $currentDay = date('jS');
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
            
            if($update){

                if(!$req->name!=null){
                    $update->name = $update->name;
                }else{
                $update->name       = $req->name;
                }
                if(!$req->price!=null){
                    $update->price = $update->price;
                }else{
                    $update->price      = $req->price;
                }
                if(!$req->image!=null){
                    $update->image = $update->image;
                }else{
                    if (file_exists(substr($update->image, 1))){ //check file's_path
                        unlink(substr($update->image, 1));
                    }
                $update->image      = $image_url;
                }

                if(!$req->color!=null){
                    $update->color = $update->color;
                }else{
                    $update->color      = $req->color;
                }
                if(!$req->description!=null){
                    $update->description = $update->description;
                }else{
                    $update->description= $req->description;
                }
                if(!$req->ram!=null){
                    $update->ram = $update->ram;
                }else{
                    $update->ram        = $req->ram;
                }
                if(!$req->storage!=null){
                    $update->storage = $update->storage;
                }else{
                    $update->storage    = $req->storage;
                }
                if(!$req->buy!=null){
                    $update->buy = $update->buy;
                }else{
                    $update->buy        = $req->buy;
                }
                if(!$req->stock!=null){
                    $update->stock = $update->stock;
                }else{
                    $update->stock      = $req->stock;
                }
                if(!$req->action!=null){
                    $update->action = $update->action;
                }else{
                    $update->action     = $req->action;
                }
                if(!$req->category_id){
                    $update->category_id = $update->category_id;
                }else{
                    $update->category_id  = $req->category_id;
                }
                // return $update->name;

                if(!$req->category_id){
                    $update->category_id = $update->category_id;
                }else{
                    $update->category_id  = $req->category_id;
                }
                $update->created_at = $update->created_at;
                $update->updated_at =  now();
            
                $result = $update->save();
                $data   = $update->refresh();
                if($result){
                return response()->json([
                    'success' => true,
                    'message' => 'You have benn updated',
                    'data'    => $data
                ],201);  
                }
                else{
                    return [
                        'success'=>false
                    ]; 
                }
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'No products'
                ],401);
            }
        
    
    }

    public function delete($id=null) {
        // $this->validate($id,[
        //     'id'=>'required',
        // ]);
        if(empty($id)){
            return response()->json([
                'success'=>false,
                'message' => 'Products id not found'
            ],404);
            // return 'g';
        }
        $article = products::find($id);
        if($article){
            $article->delete();
        return response()->json([
            'success'=>true,
            'message'=>'You have been deleted the product.'

        ],200);
        }else{
            return response()->json([
                'success'   =>false,
                'meassage'  =>'No products'
            ],404);
        }
    }

    public function user_scheme_price_list(Request $req,$pg=null){
        $offset = 0;
        $limit  = 4;
        $user = User::where('id',$req->user_id)->first();
        if($user){
          $scheme_id =  $user->scheme_id;
          $pro_price_scheme = product_price_scheme::where('scheme_id',$scheme_id)->with('products')->get();

        if($pg>0){            
            $offset=($pg-1)* $limit;
        }
         $pg?$data= product_price_scheme::where('scheme_id',$scheme_id)->with('products')->offset($offset)->limit($limit)->orderBy('id', 'DESC')->get()
         :$data= product_price_scheme::where('scheme_id',$scheme_id)->with('products')->orderBy('id', 'DESC')->get();
         $total_page=count($pro_price_scheme);

        if(count($data)==0){
            $total_page=0;
            $sum_page=0;
        }
        if(($total_page-$offset)<$limit || empty($pg)){
            $sum_page = $total_page;
        }else{
            $sum_page = $total_page-($total_page-$offset)+$limit;
        }
        for($i=0;$i<count($data);$i++){
            $price[] = $data[$i]->products->price;
            $unit_price[] = $data[$i]->unit_price;
             $data[$i]->setAttribute('margin', round($price[$i]-$unit_price[$i],2));
        }
         return response()->json([
            'success'=>true,
            'data'=>$data,
            'sum_page'=>$sum_page,
            'total_page'=>$total_page
         ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'No user id'
            ],404);
        }
    }

    public function user_scheme_price_list_detail(Request $req){
        $user = User::where('id',$req->user_id)->first();
        if($user){
          $scheme_id =  $user->scheme_id;
          $pro_price_scheme = product_price_scheme::where('product_id',$req->product_id)->where('scheme_id',$scheme_id)->with('products')->with('products.category')->first();
            if($pro_price_scheme==null){
                return response()->json([
                    'success'=>false,
                    'message'=>'Product Not Found'
                ],200);
                }else{
                    return response()->json([
                        'success'=>true,
                        'data'=>$pro_price_scheme
                    ],200);
                }
            
            if(!$pro_price_scheme){
            return response()->json([
                'success'=>false,
                'data'=>"Can't fetch data"
            ],400);
          }
         
        }    
        else{
            return response()->json([
                'success'=>false,
                'data'=>"Can't fetch data"
            ],400);
        }
    }

    public function user_scheme_price_list_delete(Request $req){
        return 'g';
            $delete = product_price_scheme::where(['scheme_id',$req->scheme_id],['product_id',$req->product_id])->delete();
       
            return response()->json([
                'success'=>false,
                'message'=>'The products ID must not be null'
            ],400);
      

    }

    public function search(Request $request,$pg=null){
        $user = User::where('id',$request->user_id)->first();
        if(!$user){
            return response()->json([
                'success'=>false,
                'message'=>'No user'
            ],400);
        }
        $scheme_id = $user->scheme_id;
        $data_search = $request->name;
        $parts = preg_split('/\s+/', $data_search);
        $lenght=count($parts);
        $pg?$data=product_price_scheme::query():$data=product_price_scheme::query();
        for($i=0;$i<$lenght;$i++){
           $data ->whereRelation('products','name', 'like' , "%{$parts[$i]}%");

        }
        $total_page=count($data->get());
         $offset = 0;
        $limit  = 4;
        if($pg>0){            
            $offset=($pg-1)* $limit;
        }
        $pg?$get_data = $data->offset($offset)->limit($limit)->orderBy('updated_at', 'DESC')->with('products')->where('scheme_id',$scheme_id)->get()
        :$get_data=$data->with('products')->where('scheme_id',$scheme_id)->get();
        if(count($get_data)==0){
            $total_page=0;
            $sum_page=0;
            return response()->json([
                'success'=>false,
                'message'=>'Products not found',
                'sum_page'=>$sum_page,
                'total_page'=>$total_page
            ],200);
        }
        if(($total_page-$offset)<$limit || empty($pg)){
            $sum_page = $total_page;
        }else{
            $sum_page = $total_page-($total_page-$offset)+$limit;
        }
            return response()->json([
                'success'=>true,
                'message'=>'Products found',
                'data'=>$get_data,
                'sum_page'=>$sum_page,
                'total_page'=>$total_page
            ],200);
    }

    public function add_to_cart(addCartRequest $req){
        $user = User::where('id',$req->user_id)->first();
        if(!$user){
            return response()->json([
                'success'=>false,
                'message'=>'No user'
            ],400);
        }else{
           $scheme_id = $user->scheme_id;
           $product_price_scheme = product_price_scheme::where('scheme_id',$scheme_id)->where('product_id',$req->product_id)->first();
           if(!$product_price_scheme){
            return response()->json([
                'success'=>false,
                'message'=>'No product'
            ],400);
           }

           $scheme_price = $product_price_scheme->unit_price;
        }
        
        $find_cart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->get();
        if(count($find_cart)==0){
            // return 'g';
        $user_id = $req->user_id;
        $product_id = $req->product_id;
        $product = products::where('id',$req->product_id)->first();
        $total = $scheme_price;
        $tbl_cart = new Cart;
        $tbl_cart->user_id = $user_id;
        $tbl_cart->product_id = $product_id;
        $tbl_cart->qty = 1;
        $tbl_cart->unit_price = $scheme_price;
        $tbl_cart->total = $total;
        $result = $tbl_cart->save();
        $get_result = $tbl_cart->refresh();
        }else{
           $cart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->first();
           $up_qty = $cart->qty; 
            Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->update(['qty'=>$up_qty+1]);
           $getCart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->with('products')->first();
           $qty = $getCart->qty;
           $price = $scheme_price;
        //    return $price;
           $total = $price*$qty;
        //    return $total; 
           $result = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->update(['total'=>$total]);
           $get_result = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->first();
        }
        // else{

        // }
        if($result){
            return response()->json([
                'success'=>true,
                'data'=>$get_result
            ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'fail connect to table cart'
            ],400);
        }
    
    }

    public function sub_to_cart(addCartRequest $req){
        $user = User::where('id',$req->user_id)->first();
        if(!$user){
            return response()->json([
                'success'=>false,
                'message'=>'No user'
            ],400);
        }else{
           $scheme_id = $user->scheme_id;
           $product_price_scheme = product_price_scheme::where('scheme_id',$scheme_id)->where('product_id',$req->product_id)->first();
           if(!$product_price_scheme){
            return response()->json([
                'success'=>false,
                'message'=>'No product'
            ],400);
           }

           $scheme_price = $product_price_scheme->unit_price;
        }
        
        $find_cart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->first();

            if($find_cart){
           $cart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->first();
           $up_qty = $cart->qty; 
            Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->update(['qty'=>$up_qty-1]);
           $getCart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->with('products')->first();
           $qty = $getCart->qty;
           $price = $scheme_price;
           $total = $price*$qty; 
           $result = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->update(['total'=>$total]);
           $get_cart = Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->first();

            if($get_cart->qty<1){
                Cart::where('user_id', $req->user_id)->where('product_id',$req->product_id)->delete();
                $get_cart = 'Cart deleted';
            }
            
            return response()->json([
                'success'=>true,
                'data'=>$get_cart
            ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'fail connect to table cart'
            ],400);
        }
    

    }

    public function delete_cart(Request $req){
      $cart = Cart::where('user_id', $req->user_id)->where('product_id', $req->product_id)->delete();
      if($cart){
        return response()->json([
            'success'=>true,
            'message'=>'Cart delted'
        ],200);
      }else{
        return response()->json([
            'success'=>false,
            'message'=>'No Cart'
        ],400);
      }

    }

    public function list_cart(Request $req){
        $data = Cart::where('user_id', $req->user_id)->with('products')->get();
        if(count($data)>0){
            // return 'g';
            for($i=0;$i<count($data);$i++){
                $total = $data[$i]->total;
                $total_qty = $data[$i]->qty;
             $array[]=$total;
             $array_qty[]=$total_qty;
             };
             $numbers = new Collection($array);
             $numbers_qty = new Collection($array_qty);
             $amount = $numbers->sum();
             $amount_qty = $numbers_qty->sum();
            return response()->json([
                'success'=>true,
                'data'=>$data,
                'amount'=>$amount,
                'amount_qty'=>$amount_qty
            ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'Cart not found'
            ],200);
        }
    }

    public function order(Request $req){
        $order_invoice = order::max('invoice');
        $data = new Order;
        $data->invoice = $order_invoice + 1;
        $data->user_id = $req->user_id;
        $data->address_id = $req->address_id;
        $data->pay_by = $req->pay_by;
        if($req->status){
              $data->status = $req->status;
        }
      
        $result = $data->save();
        // $data->load('user');
        $get_data = $data->load('user');
        if($result){
            return response()->json([
                'success'=>true,
                'data'=>$get_data
            ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'error'
            ],400);
        }
        // return 'gg';
    }

    public function order_details(Request $req){

        $objects = $req->input();
        $array = array();
       for($i=0; $i < count($objects); $i++){
        $array[] = array(
            'order_id'=>  $objects[$i]['order_id'],
            'product_id'=>$objects[$i]['product_id'],
            'qty'=>$objects[$i]['qty'],
            'unit_price'=>0,
            'discount'=>$objects[$i]['discount']
        );
       }

       $refresh = orderDetail::query();
            for($i=0; $i < count($objects); $i++){
                $refresh->orwhere('order_id' ,  $objects[$i]['order_id'] );
            }
        $result = orderDetail::insert($array);
         if($result){
            $get_refresh = $refresh->with('order')->with('product')->get();
            // return $get_refresh;
            if($get_refresh){
             $customer_id = $get_refresh[0]['order']['customer_id'];
             $user = User::find($customer_id);
             $scheme_id = $user->scheme_id;
             $scheme = scheme::where('id', $scheme_id)->first();
             $scheme_price =  $scheme->scheme_price;
            }

        // $result = orderDetail::insert($array);
       
            $refresh = orderDetail::query();
            for($i=0; $i < count($objects); $i++){
                $refresh->orwhere('order_id' ,  $objects[$i]['order_id'] );
            }
            $get_refresh = $refresh->with('order')->with('product')->get();
            for($i=0; $i < count($objects); $i++){
            //   return  $get_refresh[$i]['unit_price'] ;
             $get_refresh[$i]['unit_price'] = round(($get_refresh[$i]['product']['price'])-(($scheme_price*($get_refresh[$i]['product']['price']))/100),4);
            }
            return response()->json([
                'success'=>true,
                'data' => $get_refresh,
                // 'total'=>
            ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'error'
            ],400);
        }
            

    }

    public function show_cart(Request $request,$pg=null){
            $search_cart = count($request->input());
        $data=products::query();
       
        for($i=1;$i<=$search_cart;$i++){
            $data ->orwhere('id',$request[$i-1]['id'.$i]);
        }
        $total_page=count($data->get());
         $offset = 0;
        $limit  = 4;
        if($pg>0){            
            $offset=($pg-1)* $limit;
        }
        $pg?$get_data = $data->offset($offset)->limit($limit)->orderBy('updated_at', 'DESC')->get():$get_data=$data->get();
        if(count($get_data)==0){
            $total_page=0;
            $sum_page=0;
            return response()->json([
                'success'=>true,
                'message'=>'Products not found',
                'data'=>$get_data,
                'sum_page'=>$sum_page,
                'total_page'=>$total_page
            ],404);
        }
        if(($total_page-$offset)<$limit || empty($pg)){
            $sum_page = $total_page;
        }else{
            $sum_page = $total_page-($total_page-$offset)+$limit;
        }
            return response()->json([
                'success'=>true,
                'message'=>'Products found',
                'data'=>$get_data,
                'sum_page'=>$sum_page,
                'total_page'=>$total_page
            ],200);
    }

    public function order_detailss(Request $req){

        $objects = $req->input();
        $array = array();
       for($i=0; $i < count($objects); $i++){
        $array[] = array(
            'order_id'=>  $objects[$i]['order_id'],
            'product_id'=>$objects[$i]['product_id'],
            'qty'=>$objects[$i]['qty'],
            'unit_price'=>0,
            'discount'=>$objects[$i]['discount']
        );
       }

       $refresh = orderDetail::query();
            for($i=0; $i < count($objects); $i++){
                $refresh->orwhere('order_id' ,  $objects[$i]['order_id'] );
            }
        $result = orderDetail::insert($array);
         if($result){
            $get_refresh = $refresh->with('order')->with('product')->get();
            // return $get_refresh;
            if($get_refresh){
             $customer_id = $get_refresh[0]['order']['customer_id'];
             $user = User::find($customer_id);
             $scheme_id = $user->scheme_id;
             $scheme = scheme::where('id', $scheme_id)->first();
             $scheme_price =  $scheme->scheme_price;
            }

        // $result = orderDetail::insert($array);
       
            $refresh = orderDetail::query();
            for($i=0; $i < count($objects); $i++){
                $refresh->orwhere('order_id' ,  $objects[$i]['order_id'] );
            }
            $get_refresh = $refresh->with('order')->with('product')->get();
            for($i=0; $i < count($objects); $i++){
                $customer_id = $get_refresh[0]['order']['customer_id'];
                 $user = User::find($customer_id);
                 $scheme_id = $user->scheme_id;
                 $pro_scheme_price = product_price_scheme::where('scheme_id', $scheme_id)->where('product_id','')->first();
                 $scheme_price =  $scheme->scheme_price;
            //   return  $get_refresh[$i]['unit_price'] ;
            //  $get_refresh[$i]['unit_price'] = round(($get_refresh[$i]['product']['price'])-(($scheme_price*($get_refresh[$i]['product']['price']))/100),4);
             $get_refresh[$i]['unit_price'] = 100;
            }
            return response()->json([
                'success'=>true,
                'data' => $get_refresh,
                // 'total'=>
            ],200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'error'
            ],400);
        }
            

    }

    public function order_detail(Request $req){
        $objects = $req->input();
        $order = order::where('id',$objects[0]['order_id'])->get();
        $order_id =  $order[0]['id'];
        $user_id = $order[0]['user_id'];

        for($i=0;$i<count($objects);$i++){
            $array[] = array(
                'order_id'=>$objects[$i]['order_id'],
                'product_id'=>$objects[$i]['product_id'],
                'qty'=>$objects[$i]['qty'],
                'unit_price'=>$objects[$i]['unit_price'],
                'discount'=>$objects[$i]['discount'],
                'total'=>$objects[$i]['unit_price']*$objects[$i]['qty']
             ) ;

            $arrayOfProduct_id [] = $array[$i]['product_id'];

        }
        $order_detail = orderDetail::insert($array);
        if(!$order_detail){
            return response()->json([
                'success'=>false,
                'message'=>'Error Checkout'
            ],400);
        }else{
            Cart::where('user_id',$user_id)->whereIn('product_id',$arrayOfProduct_id)->delete();
            $get_order_detail =orderDetail::where('order_id',$order_id)->get();
            return response()->json([
                'success'=>true,
                'data'=>$get_order_detail
            ],200);
        }

    }

    public function list_ordered(Request $req){
       $order_detail = orderDetail::whereRelation('order', 'user_id', $req->user_id)->whereRelation('order', 'status', 'delivered')->with('order','product')->get();
  
        if(count($order_detail)==0){
            return response()->json([
                'success'=>false,
                'message'=>'No Products'
            ],200);
        }else{
            //make group by invoice
            $groupedInvoice = $order_detail->groupBy('order.invoice');
            $collections = collect($groupedInvoice);
            // covert obj type to array type =>{"0":100,"3":2345}
            $groupedInvoices = $collections->toArray();
            //get all invoice value in data total 6 invocie
            for($a=0;$a<count($order_detail);$a++){
                $invoice[] = $order_detail[$a]['order']['invoice'];
            }
            // but have the same invocie so, convert by unique 100(3)&2345(3) =>{"0":100,"3":2345}
            $collection = collect($invoice);
            $uniqueincoive = $collection->unique();

            //loop 0-1 
           for($i=0;$i<count($uniqueincoive);$i++){ 
               // find value of invocie (100&2345) type obj =>{"0":100,"3":2345}
               $collection = collect($uniqueincoive);
            // covert obj type to array type =>{"0":100,"3":2345}
            $arrays = $collection->toArray();
            // covert key of obj to array =>[0,3]
            $keys = array_keys($arrays);
            // get value of obj point by key cus $keys[0]=100,$keys[1]=2345 =>$arrays[0]&$array[3] =>getInvoice [100,2345]
            $getInvoice[] = $arrays[$keys[$i]];
            // groupInvoice have key 100 & 2345 of 6 data so, we poit key of group and insert into array 
            // =>get_group [[],[]]
            $get_group[] = $groupedInvoice[$getInvoice[$i]];
            $total[] = array_sum(array_column($groupedInvoices[$getInvoice[$i]], 'total'));
           }
            return response()->json([
                'success'=>true,
                'data'=>$get_group,
                'invoice'=>$getInvoice,
                'total' => $total
            ],200);
        }
    }

    public function list_category(){
        $get = category::all();
            if($get){
            return response()->json([
                'success' =>true,
                'data'=>$get
            ],200);
            }else{
                return response()->json([
                    'success' =>true,
                    'message'=>'No category data'
                ],400);
            }
    }

    public function add_category(Request $request){
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
            $data->des = $request->des;
            $data->image = $image_url;
            $insert = $data->save();
            $get_data = $data->refresh();
            if($insert){
                return response()->json([
                    'success'=>true,
                    'data'=>$get_data
                ],200);
            }else{
                return response()->json([
                    'success'=>true,
                    'data'=>$get_data
                ],200);
            }

        }
    }

    public function user_scheme_price_list_by_category(Request $req){
        $user = User::where('id',$req->user_id)->first();
        if($user){
          $scheme_id =  $user->scheme_id;
          $pro_price_scheme = product_price_scheme::whereRelation('products', 'category_id', $req->category_id)->where('scheme_id',$scheme_id)->with('products')->with('products.category')->get();
          if(count($pro_price_scheme)==0){
            //get category name
            $category = category::where('id',$req->category_id)->first();
            if($category){
                return response()->json([
                    'success'=>false,
                    'message'=>'No products',
                    'category_name'=>$category->name
                ],200);
            }else{
                return response()->json([
                    'success'=>false,
                    'message'=>'No Category Id',
                ],400);
            }
          }else{
            return response()->json([
                'success'=>true,
                'data'=>$pro_price_scheme,
                'category_name'=>$pro_price_scheme[0]['products']['category']['name']
            ],200);
          }
        }else{
            return response()->json([
                'success'=>false,
                'message'=>'No user'
            ],400);
        }
    }

    public function add_slide(Request $request){
        $slide_order = slide::max('slide_order');
        if($slide_order==0){
         $slide_order  = 0;
        }else{
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
         $insert->image =$image_url;
         $insert->slide_order  = $slide_order+1;
         $insert->title  = $request->title;
         $insert->tage  = $request->tage;
         $insert->link  = $request->link;
         $insert->action  = $request->action;
         $insert_slide = $insert->save(); 
         $get_insert = $insert->refresh();  
         if(!$insert_slide){
             return response()->json([
                 'success' =>false,
                 'message'=>'Error '
             ],400);
         }else{
             return response()->json([
                 'success' =>true,
                 'data'=>$get_insert
             ],200);
         }  
 
       
         }else{
             return response()->json([
                 'success'=>false,
                 'mesaage'=>'No Files image'
             ],400);
         }
     }

    
     public function list_slide(){
        $slide = slide::orderBy('slide_order', 'asc')->get();
        if(!$slide){
            return response()->json([
                'success' =>false,
                'message' =>'Not found slide'
            ],404);
        }
        return response()->json([
            'success'=>true,
            'data'=>$slide
        ],200);
    }

    public function delete_slide(Request $req){
        $deleted = slide::where('id', $req->id)->delete();
        if(!$deleted){
            return response()->json([
                'success' =>false,
                'message'=>'No slide'
            ],400);
        }else{
            return response()->json([
                'success'=>true,
                'message'=>'You has been deleted the slide'
            ],200);
        }    
    }

    public function detail_slide($slide_id=null){
        $get_slide = slide::where('id',$slide_id)->first();
        if($slide_id){
                if($get_slide){
                return response()->json([
                    'success' =>true,
                    'data'=>$get_slide
                ],200);
            }else{
                return response()->json([
                    'success' =>false,
                    'message'=>'slide not found'
                ],400);
            }

        }else{
            return response()->json([
                'success' =>false,
                'message'=>'The slide ID must not be null '
            ],400);
        }
       
    }

    public function update_slide(UpdateSlideRequest $req){

        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        //find slide 
        $get_slide = slide::where('id',$req->id)->first(); 
        
        if($get_slide){

            $old_img = $get_slide->image;
            if($req->new_image!=null){
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
                $path = public_path(substr($get_slide->image, 1));
                if(file_exists($path)){
                    unlink(substr($get_slide->image, 1));
                }
            }else{
                $new_image = $get_slide->image;
            }
            if($req->new_order!=null){
                        $slide_order = slide::max('slide_order');
                        $where_slide_order = slide::where('id',$req->id)->first();
                        $old_slide_order =$where_slide_order->slide_order;
                        $slide = slide::all();
                    if($old_slide_order==$slide_order ){
                        if($req->new_order>$old_slide_order){
                            return response()->json([
                                'success' => false,
                                'message'=>'The new order is out of the maximum order'
                            ],400);
                        }
                            if($req->new_order==$old_slide_order){
                                
                                $new_Order = $old_slide_order;
                            }else{
                                slide::where('id',$req->id)->update(['slide_order'=>$req->new_order]);
                                // return $old_slide_order;
                                slide::where('id', '!=', $req->id)->where('slide_order',$req->new_order)->update(['slide_order'=>$old_slide_order]);
                            }    
                        }
                        else{
                            if($req->new_order>$slide_order){
                                return response()->json([
                                    'success' => false,
                                    'message'=>'The new order is out of the maximum order'
                                ],400);
                            }
                            if($req->new_order==$slide_order){

                                $new_Order = $slide_order;
                                // return $new_Order;
                                

                                slide::where('id',$req->id)->update(['slide_order'=>$new_Order]);
                                // return $old_slide_order;
                                slide::where('id', '!=', $req->id)->where('slide_order',$slide_order)->update(['slide_order'=>$old_slide_order]);
                            }else{
                                slide::where('id',$req->id)->update(['slide_order'=>$req->new_order]);
                                // return $old_slide_order;
                                slide::where('id', '!=', $req->id)->where('slide_order',$req->new_order)->update(['slide_order'=>$old_slide_order]);
                                
                            }
                        }
                        $get_slide = slide::where('id',$req->id)->first(); 
                        $new_order = $get_slide->slide_order;
            }else{
                $new_order = $get_slide->slide_order;
            }
            if($req->new_title!=null){
                $new_title = $req->new_title;
            }else{
                $new_title = $get_slide->title;
            }
            if($req->new_tage!=null){
               $new_tage = $req->new_tage;
            }else{
                $new_tage = $get_slide->tage;
            }
            if($req->new_link!=null){
                $new_link = $req->new_link;
            }else{
                $new_link = $get_slide->link;
            }if($req->action!=null){
                $action = $req->action;
            }else{
                $action = $get_slide->action;
            }

            $update_slide = slide::where('id', $req->id)->update(['image'=>$new_image,'slide_order'=>$new_order,'title'=>$new_title,'tage'=>$new_tage,'link'=>$new_link,'action'=>$action]);
            return response()->json([
                'success' => true,
                'meesage'=>'The slide has been updated'
            ],200);
        }else{
            return response()->json([
                'success' =>false,
                'message'=>'The slide ID Not found'
            ],400);
        }

       
    }

}
