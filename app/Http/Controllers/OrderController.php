<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $pg = $req->query('page');
        $offset = 0;
        $limit  = 4;
        if ($pg > 0) {
            $offset = ($pg - 1) * $limit;
        }

        $pg ? $order_detail = OrderDetail::whereRelation('order', 'status', 'delivered')->whereRelation('order', 'user_id', $req->user_id)->with('order', 'product')->get()->groupBy('order.invoice')->skip($offset)->take($limit) :
            $order_detail = OrderDetail::whereRelation('order', 'status', 'delivered')->whereRelation('order', 'user_id', $req->user_id)->with('order', 'product')->get()->groupBy('order.invoice');
        $total_invoice = count(OrderDetail::whereRelation('order', 'status', 'delivered')->whereRelation('order', 'user_id', $req->user_id)->with('order', 'product')->get()->groupBy('order.invoice'));
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
            $myKeys = array_keys($groupedInvoices);
            for ($a = 0; $a < count($groupedInvoices); $a++) {
                $invoice[] = $groupedInvoices[$myKeys[$a]][0]['order']['invoice'];
            }
            $collection = collect($invoice);
            $uniqueincoive = $collection->unique();
            $date =  OrderDetail::query();
            for ($i = 0; $i < count($uniqueincoive); $i++) {
                $collection = collect($uniqueincoive);
                $arrays = $collection->toArray();
                $keys = array_keys($arrays);
                $getInvoice[] = $arrays[$keys[$i]];
                $date->orwhereRelation('order', 'invoice', $arrays[$keys[$i]]);
                $get_group[] = $order_detail[$getInvoice[$i]];

                $total[] = array_sum(array_column($groupedInvoices[$getInvoice[$i]], 'total'));
            }
            $getInvoice_ordered = implode(',', $getInvoice);
            $date = Order::whereIn('invoice', $getInvoice)->orderByRaw("FIELD(invoice, $getInvoice_ordered)")->with('user')->get();

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
                'total_invocie' => $total_invoice,
                'total_page' => ceil($total_invoice / $limit),
                'invoice' => $getInvoice,
                'invoice_date' => $invoice_date,
                'buy_by' => $buy_by_name,
                'total' => $total,
            ], 200);
        }
    }

    public function showOrderDetail(Request $req)
    {
        $objects = $req->input();
        $order = Order::where('id', $objects[0]['order_id'])->get();
        $order_id =  $order[0]['id'];
        $user_id = $order[0]['user_id'];

        for ($i = 0; $i < count($objects); $i++) {
            $array[] = array(
                'order_id' => $objects[$i]['order_id'],
                'product_id' => $objects[$i]['product_id'],
                'qty' => $objects[$i]['qty'],
                'unit_price' => $objects[$i]['unit_price'],
                'discount' => $objects[$i]['discount'],
                'total' => $objects[$i]['unit_price'] * $objects[$i]['qty']
            );

            $arrayOfProduct_id[] = $array[$i]['product_id'];
        }
        $order_detail = OrderDetail::insert($array);
        if (!$order_detail) {
            return response()->json([
                'success' => false,
                'message' => 'Error Checkout'
            ], 400);
        } else {
            Cart::where('user_id', $user_id)->whereIn('product_id', $arrayOfProduct_id)->delete();
            $get_order_detail = OrderDetail::where('order_id', $order_id)->get();
            return response()->json([
                'success' => true,
                'data' => $get_order_detail
            ], 200);
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
        $order_invoice = Order::max('invoice');
        $data = new Order;
        $data->invoice = $order_invoice + 1;
        $data->user_id = $request->user_id;
        // $data->address_id = $request->address_id;
        $data->pay_by = $request->pay_by;
        $data->status = OrderStatusEnum::DELIVERED;
        $result = $data->save();
        $get_data = $data->load('user');
        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $get_data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'error'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
