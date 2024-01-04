<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
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

        $pg ? $order_detail = OrderDetail::whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice')->skip($offset)->take($limit) :
            $order_detail = OrderDetail::whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice');
        $total_invoice = count(OrderDetail::whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice'));
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
            $date =  OrderDetail::query();
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
                'total_invoice' => $total_invoice,
                'total_page' => ceil($total_invoice / $limit),
                'invoice' => $getInvoice,
                'invoice_date' => $invoice_date,
                'buy_by' => $buy_by_name,
                'total' => $total,
            ], 200);
        }
    }

    public function search (Request $request) {
        $order_detail = OrderDetail::whereRelation('order', 'invoice', $request->invoice)->whereRelation('order', 'status', 'delivered')->with('order', 'product')->get();
        $total_invoice = count(OrderDetail::whereRelation('order', 'invoice', $request->invoice)->whereRelation('order', 'status', 'delivered')->with('order', 'product')->get()->groupBy('order.invoice'));

        if (count($order_detail) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'No Products',
            ], 200);
        } else {
            $groupedInvoice = $order_detail->groupBy('order.invoice');
            $collections = collect($groupedInvoice);
            $groupedInvoices = $collections->toArray();

            for ($a = 0; $a < count($order_detail); $a++) {
                $invoice[] = $order_detail[$a]['order']['invoice'];
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
                $get_group[] = $groupedInvoice[$getInvoice[$i]];

                $total[] = array_sum(array_column($groupedInvoices[$getInvoice[$i]], 'total'));
            }

            return response()->json([
                'success' => true,
                'data' => $get_group,
                'invoice' => $getInvoice,
                'total' => $total,
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
        //
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
    public function destroy(string $invoice = null)
    {
        if ($invoice == null) {
            return response()->json([
                'success' => false,
                'message' => 'The ID must not be null.'
            ], 400);
        } else {
            $ordered =  OrderDetail::whereRelation('order', 'invoice', $invoice)->delete();
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
}
