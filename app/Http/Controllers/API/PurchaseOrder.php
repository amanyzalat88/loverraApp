<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\PurchaseOrderResource;

use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\PurchaseOrderProduct as PurchaseOrderProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Product as ProductModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;

class PurchaseOrder extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = PurchaseOrderModel::select('purchase_orders.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->statusJoin()
            ->createdUser()

            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->get();

            $purchase_orders = PurchaseOrderResource::collection($query);
           
            $total_count = PurchaseOrderModel::select("id")->get()->count();

            $item_array = [];
            foreach($purchase_orders as $key => $purchase_order){
                
                $purchase_order = $purchase_order->toArray($request);

                $item_array[$key][] = $purchase_order['po_number'];
                $item_array[$key][] = ($purchase_order['po_reference'] != '')?$purchase_order['po_reference']:'-';
                $item_array[$key][] = ($purchase_order['supplier_name'] != '')?$purchase_order['supplier_name']:'-';
                $item_array[$key][] = ($purchase_order['order_date'] != '')?$purchase_order['order_date']:'-';
                $item_array[$key][] = ($purchase_order['order_due_date'] != '')?$purchase_order['order_due_date']:'-';
                $item_array[$key][] = $purchase_order['total_order_amount'];
                $item_array[$key][] = view('common.status', ['status_data' => ['label' => $purchase_order['status']['label'], "color" => $purchase_order['status']['color']]])->render();
                $item_array[$key][] = $purchase_order['created_at_label'];
                $item_array[$key][] = $purchase_order['updated_at_label'];
                $item_array[$key][] = (isset($purchase_order['created_by']) && $purchase_order['created_by']['fullname'] != '')?$purchase_order['created_by']['fullname']:'-';
                $item_array[$key][] = view('purchase_order.layouts.purchase_order_actions', array('purchase_order' => $purchase_order))->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_PURCHASE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            DB::beginTransaction();

            $po_data = $this->form_po_array($request);
            
            if(!empty($po_data['po_data'])){
                
                $po = $po_data['po_data'];
                
                $po['slack'] = $this->generate_slack("purchase_orders");
                $po['created_at'] = now();
                $po['created_by'] = $request->logged_user_id;

                $po_id = PurchaseOrderModel::create($po)->id;
            }
            
            if(!empty($po_data['po_products'])){
                
                $po_products = $po_data['po_products'];

                array_walk($po_products, function (&$item, $key) use ($po_id, $request){
                    
                    $item['slack'] = $this->generate_slack("purchase_order_products");
                    $item['purchase_order_id'] = $po_id; 
                    $item['created_at'] = now();
                    $item['created_by'] = $request->logged_user_id;

                    PurchaseOrderProductModel::insert($item);

                });
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Purchase order created successfully", 
                    "data"    => $po['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_PURCHASE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $po_details = PurchaseOrderModel::where('slack', $slack)->first();

            DB::beginTransaction();

            $request->po_slack = $slack;

            $po_data = $this->form_po_array($request);
            
            if(!empty($po_data['po_data'])){
                
                $po = $po_data['po_data'];
            
                $po['updated_at'] = now();
                $po['updated_by'] = $request->logged_user_id;

                $action_response = PurchaseOrderModel::where('slack', $slack)
                ->update($po);
            }
            
            $po_id = $po_details->id;

            if(!empty($po_data['po_products'])){
                
                if(count($po_data['po_products']) > 0){
                    PurchaseOrderProductModel::where('purchase_order_id', $po_id)->delete();
                }

                $po_products = $po_data['po_products'];

                array_walk($po_products, function (&$item, $key) use ($po_id, $request){

                    $item['slack'] = $this->generate_slack("purchase_order_products");
                    $item['purchase_order_id'] = $po_id;
                    $item['updated_at'] = now();
                    $item['updated_by'] = $request->logged_user_id;

                    PurchaseOrderProductModel::insert($item);

                });
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Purchase order updated successfully", 
                    "data"    => $po_details['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function update_po_status(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_STATUS_PURCHASE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $status_constant = $request->status;
            
            $po_details = PurchaseOrderModel::where('slack', $slack)->first();
            if(empty($po_details)){
                throw new Exception("Invalid Purchase Order selected");
            }

            $po_status = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status_constant)],
                ['key', '=', 'PURCHASE_ORDER_STATUS'],
                ['status', '=', '1']
            ])->active()->first();
            if(empty($po_status)){
                throw new Exception("Invalid status provided");
            }

            DB::beginTransaction();
            
            $po = [];
            $po['status'] = $po_status->value;
            $po['updated_at'] = now();
            $po['updated_by'] = $request->logged_user_id;

            $action_response = PurchaseOrderModel::where('slack', $slack)
            ->update($po);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Purchase order status changed successfully", 
                    "data"    => $po_details['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function form_po_array($request){
        
        $po_slack = $request->po_slack;

        $products = $request->products;

        if( empty((array) $products) ){
            throw new Exception("Product list cannot be empty");
        }

        $supplier_data = SupplierModel::select('id', 'name', 'supplier_code', 'name', 'address', 'pincode')
        ->where('slack', '=', trim($request->supplier))
        ->active()
        ->first();
        if (empty($supplier_data)) {
            throw new Exception("Invalid supplier selected", 400);
        }

        $po_number_details = PurchaseOrderModel::where([
            ['po_reference', '=', trim($request->po_reference)],
            ['status', '!=', 0],
        ])
        ->when($po_slack, function ($query, $po_slack) {
            return $query->where('slack', '!=', $po_slack);
        })->first();
        if(!empty($po_number_details)){
            throw new Exception("Purchase order reference no ".trim($request->po_reference)." is already used");
        }

        $po_ref_details = PurchaseOrderModel::where([
            ['po_number', '=', trim($request->po_number)],
            ['status', '!=', 0],
        ])->when($po_slack, function ($query, $po_slack) {
            return $query->where('slack', '!=', $po_slack);
        })->first();
        if(!empty($po_ref_details)){
            throw new Exception("Purchase order no ".trim($request->po_number)." is already used");
        }

        $currency_data = CountryModel::select('currency_code', 'currency_name')
        ->where('currency_code', '=', trim($request->currency))
        ->active()
        ->first();
        if (empty($currency_data)) {
            throw new Exception("Invalid currency selected", 400);
        }

        foreach($products as $product_key => $product_value){

            $product_slack = $product_value['slack'];
            $product_name = $product_value['name'];
            $unit_price = (isset($product_value['unit_price']) && $product_value['unit_price'] != '')?$product_value['unit_price']:0.00;
            $quantity = (isset($product_value['quantity']) && $product_value['quantity'] != '')?$product_value['quantity']:0.00;
            $discount_percentage = (isset($product_value['discount_percentage']) && $product_value['discount_percentage'] != '')?$product_value['discount_percentage']:0.00;
            $tax_percentage = (isset($product_value['tax_percentage']) && $product_value['tax_percentage'] != '')?$product_value['tax_percentage']:0.00;

            $subtotal_amount_excluding_tax = $unit_price*$quantity;

            $discount_amount = $this->calculate_discount($subtotal_amount_excluding_tax, $discount_percentage);

            $total_amount_after_discount = ($subtotal_amount_excluding_tax-$discount_amount);

            $tax_amount = $this->calculate_tax($total_amount_after_discount, $tax_percentage);
            
            $item_total = ($total_amount_after_discount+$tax_amount);

            if($product_slack != ''){
                $product_data = ProductModel::select('products.id', 'products.slack', 'products.product_code')
                ->where('products.slack', '=', $product_slack)
                ->categoryJoin()
                ->supplierJoin()
                ->taxcodeJoin()
                ->discountcodeJoin()
                ->categoryActive()
                ->supplierActive()
                ->taxcodeActive()
                ->first();
                if (empty($product_data)) {
                    throw new Exception("Product ".$product_name." is not currently available", 400);
                }
            }
            
            $po_products[] = [
                'purchase_order_id' => 0,
                'product_slack' => ($product_slack != '')?$product_data->slack:NULL,
                'product_id' => ($product_slack != '')?$product_data->id:NULL,
                'product_code' => ($product_slack != '')?$product_data->product_code:NULL,
                'name' => isset($product_name)?$product_name:NULL,
                
                'quantity' => $quantity,
                'amount_excluding_tax' => $unit_price,
                'subtotal_amount_excluding_tax' => $subtotal_amount_excluding_tax,
                'discount_percentage' => $discount_percentage,
                
                'tax_percentage' => $tax_percentage,
                'discount_amount' => $discount_amount,
                'total_after_discount' => $total_amount_after_discount,

                'tax_amount' => $tax_amount,
                'total_amount' => $item_total,
            ];
        }

        $total_amount_excluding_tax_array = data_get($po_products, '*.subtotal_amount_excluding_tax', 0);
        $total_amount_excluding_tax = array_sum($total_amount_excluding_tax_array);

        $total_discount_amount_array = data_get($po_products, '*.discount_amount', 0);
        $total_discount_amount = array_sum($total_discount_amount_array);

        $total_after_discount_amount_array = data_get($po_products, '*.total_after_discount', 0);
        $total_after_discount_amount = array_sum($total_after_discount_amount_array);

        $total_tax_amount_array = data_get($po_products, '*.tax_amount', 0);
        $total_tax_amount = array_sum($total_tax_amount_array);

        $shipping_charge = (isset($request->shipping_charge))?$request->shipping_charge:0.00;
        $packing_charge = (isset($request->packing_charge))?$request->packing_charge:0.00;

        $total_order_amount = ($total_after_discount_amount+$total_tax_amount+$shipping_charge+$packing_charge);

        $purchase_order = [
            "store_id" => $request->logged_user_store_id,
            "po_number" => $request->po_number,
            "po_reference" => $request->po_reference,
            "order_date" => $request->order_date,
            "order_due_date" => $request->order_due_date,
            "supplier_id" => $supplier_data->id,
            "supplier_code" => $supplier_data->supplier_code,
            "supplier_name" => $supplier_data->name,
            "supplier_address" => $supplier_data->address .', '.$supplier_data->pincode,
            "currency_name" => $currency_data->currency_name,
            "currency_code" => $currency_data->currency_code,
            "subtotal_excluding_tax" => $total_amount_excluding_tax,
            "total_discount_amount" => $total_discount_amount,
            "total_after_discount" => $total_after_discount_amount,
            "total_tax_amount" => $total_tax_amount,
            "shipping_charge" => $shipping_charge,
            "packing_charge" => $packing_charge,
            "total_order_amount" => $total_order_amount
        ];

        return [
            'po_data' => $purchase_order,
            'po_products' => $po_products
        ];
    }

    public function calculate_tax($item_total, $tax_percentage){
        $tax_amount = ($tax_percentage/100)*$item_total;
        return $tax_amount;
    }

    public function calculate_discount($item_total, $discount_percentage){
        $discount_amount = ($discount_percentage/100)*$item_total;
        return $discount_amount;
    }

    public function filter_purchase_orders(Request $request){
        try{

            $keyword = $request->keyword;

            $po_list = PurchaseOrderModel::select("*")
            ->where('po_number', 'like', $keyword.'%')
            ->orWhere('po_reference', 'like', $keyword.'%')
            ->orWhere('supplier_code', 'like', $keyword.'%')
            ->orWhere('supplier_name', 'like', $keyword.'%')
            ->limit(25)
            ->get();
            
            $pos = PurchaseOrderResource::collection($po_list);
           
            return response()->json($this->generate_response(
                array(
                    "message" => "Purchase orders filtered successfully", 
                    "data" => $pos
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function validate_request($request)
    {
        $request->merge(['products' => json_decode($request->products, true)]);

        $validator = Validator::make($request->all(), [
            'supplier' => $this->get_validation_rules("slack", true),
            'po_number' => 'max:50|required',
            'po_reference' => 'max:30',
            'order_date' => 'date|nullable',
            'order_due_date' => 'date|nullable|after_or_equal:order_date',
            'products.*.name' => $this->get_validation_rules("name_label", true),
            'products.*.quantity' => 'min:1|'.$this->get_validation_rules("numeric", true),
            'products.*.unit_price' => $this->get_validation_rules("numeric", true),
            'products.*.discount_percentage' => $this->get_validation_rules("numeric", false),
            'products.*.tax_percentage' => $this->get_validation_rules("numeric", false)
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
