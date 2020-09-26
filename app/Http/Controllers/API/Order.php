<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\OrderResource;

use App\Models\Order as OrderModel;
use App\Models\OrderProduct as OrderProductModel;
use App\Models\Product as ProductModel;
use App\Models\Customer as CustomerModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\Store as StoreModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\PaymentMethod as PaymentMethodModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class Order extends Controller
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
            
            $query = OrderModel::select('orders.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $orders = OrderResource::collection($query);
           
            $total_count = OrderModel::select("id")->get()->count();

            $item_array = [];
            foreach($orders as $key => $order){

                $order = $order->toArray($request);

                $item_array[$key][] = $order['order_number'];
                $item_array[$key][] = (!empty($order['customer_phone']))?$order['customer_phone']:'-';
                $item_array[$key][] = (!empty($order['customer_email']))?$order['customer_email']:'-';
                $item_array[$key][] = $order['total_order_amount'];
                $item_array[$key][] = view('common.status', ['status_data' => ['label' => $order['status']['label'], "color" => $order['status']['color']]])->render();
                $item_array[$key][] = $order['created_at_label'];
                $item_array[$key][] = $order['updated_at_label'];
              //  $item_array[$key][] = (isset($order['created_by']) && $order['created_by']['fullname'] != '')?$order['created_by']['fullname']:'-';
                $item_array[$key][] = view('order.layouts.order_actions', ['order' => $order])->render();

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
        //
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
            $validator = Validator::make($request->all(), [
                'order_status' => $this->get_validation_rules("order_status", true),
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            if(!check_access(['A_ADD_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $cart = json_decode($request->cart);

            DB::beginTransaction();

            if(!empty($cart)){

                $order_data = $this->form_order_array($request);
                
                if(!empty($order_data['order_data']) && !empty($order_data['order_products_data'])){
                    if(!empty($order_data['order_data'])){
                        
                        $order = $order_data['order_data'];
                        
                        $order['slack'] = $this->generate_slack("orders");
                        $order['store_id'] = $request->logged_user_store_id;
                        $order['order_number'] = uniqid();
                        $order['created_at'] = now();
                        $order['created_by'] = $request->logged_user_id;

                        $order_id = OrderModel::create($order)->id;

                        $code_start_config = Config::get('constants.unique_code_start.order');
                        $code_start = (isset($code_start_config))?$code_start_config:100;
                        
                        $order_number = [
                            "order_number" => $code_start+$order_id
                        ];
                        OrderModel::where('id', $order_id)
                        ->update($order_number);
                    }
                    
                    if(!empty($order_data['order_products_data'])){
                        
                        $order_products = $order_data['order_products_data'];

                        array_walk($order_products, function (&$item, $key) use ($order_id, $request){
                            
                            $item['slack'] = $this->generate_slack("order_products");
                            $item['order_id'] = $order_id; 
                            $item['created_at'] = now();
                            $item['created_by'] = $request->logged_user_id;

                            OrderProductModel::insert($item);

                            if($request->order_status == 'CLOSE' && $item['product_id'] != '' && $item['quantity']>0){
                                $product = ProductModel::find($item['product_id']);
                                $product->decrement('quantity', $item['quantity']);
                            }

                        });
                    }
                }
            }

            DB::commit();

            $forward_link = '';
            if($request->order_status == "CLOSE"){
                $forward_link = route('print_order', ['slack' => $order['slack']]);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Order created successfully", 
                    "data" => $order['slack'],
                    "link" => $forward_link
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_status' => $this->get_validation_rules("order_status", true),
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            if(!check_access(['A_EDIT_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $order_details = OrderModel::where('slack', $slack)->first();

            $cart = json_decode($request->cart);

            DB::beginTransaction();

            if(!empty($cart)){

                $order_data = $this->form_order_array($request);
                
                if(!empty($order_data['order_data']) && !empty($order_data['order_products_data'])){
                    if(!empty($order_data['order_data'])){
                        
                        $order = $order_data['order_data'];
                        
                        $order['updated_at'] = now();
                        $order['updated_by'] = $request->logged_user_id;

                        $action_response = OrderModel::where('slack', $slack)
                        ->update($order);
                    }
                    
                    $order_id = $order_details->id;

                    if(!empty($order_data['order_products_data'])){

                        if(count($order_data['order_products_data']) > 0){
                            OrderProductModel::where('order_id', $order_id)->delete();
                        }

                        $order_products = $order_data['order_products_data'];

                        array_walk($order_products, function (&$item, $key) use ($order_id, $request){
                            
                            $item['slack'] = $this->generate_slack("order_products");
                            $item['order_id'] = $order_id; 
                            $item['updated_at'] = now();
                            $item['updated_by'] = $request->logged_user_id;

                            OrderProductModel::insert($item);

                            if($request->order_status == 'CLOSE' && $item['product_id'] != '' && $item['quantity']>0){
                                $product = ProductModel::find($item['product_id']);
                                $product->decrement('quantity', $item['quantity']);
                            }

                        });
                    }
                }
            }

            DB::commit();

            $forward_link = '';
            if($request->order_status == "CLOSE"){
                $forward_link = route('print_order', ['slack' => $slack]);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Order updated successfully", 
                    "data"    => $slack,
                    "link"    => $forward_link
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
    public function destroy($slack)
    {
        try{

            if(!check_access(['A_DELETE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $order_detail = OrderModel::select('id')->where('slack', $slack)->first();
            $order_id = $order_detail->id;
            $order_products = OrderProductModel::where('order_id', $order_id)->get()->toArray();

            DB::beginTransaction();

            array_walk($order_products, function (&$item, $key){

                $product = ProductModel::find($item['product_id']);
                $product->increment('quantity', $item['quantity']);

            });

            OrderProductModel::where('order_id', $order_id)->delete();
            OrderModel::where('id', $order_id)->delete();

            DB::commit();

            $forward_link = route('orders');

            return response()->json($this->generate_response(
                array(
                    "message" => "Order deleted successfully", 
                    "data" => $slack,
                    "link" => $forward_link
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

    public function form_order_array($request){

        $cart = json_decode($request->cart);
        if( empty((array) $cart) ){
            throw new Exception("Cart cannot be empty");
        }

        if(!empty($cart)){

            switch($request->order_status){
                case 'HOLD':
                    $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'HOLD')->first();
                    $order_status = $status_data->value;
                break;
                case 'CLOSE':
                    $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();
                    $order_status = $status_data->value;
                break;
            }

            $payment_method = PaymentMethodModel::select('id', 'slack', 'label')
            ->where([
                ['payment_methods.slack', '=', $request->payment_method]
            ])
            ->active()
            ->first();
            if (empty($payment_method)) {
                throw new Exception("Invalid Payment method selected");
            }

            $store_data = StoreModel::select('tax_code_id', 'discount_code_id', 'tax_codes.tax_code', 'discount_codes.discount_code', 'tax_codes.total_tax_percentage', 'discount_codes.discount_percentage')
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->where([
                ['stores.id', '=', $request->logged_user_store_id],
                ['stores.status', '=', 1]
            ])
            ->first();
            if (empty($store_data)) {
                throw new Exception("Invalid store selected");
            }

            $store_level_total_tax_percentage = isset($store_data->total_tax_percentage)?$store_data->total_tax_percentage:0.00;
            $store_level_total_discount_percentage = isset($store_data->discount_percentage)?$store_data->discount_percentage:0.00;

            foreach($cart as $cart_item_key => $cart_item){

                $product_data = ProductModel::select('products.*','tax_codes.id as tax_code_id', 'discount_codes.id as discount_code_id', 'tax_codes.tax_code', 'discount_codes.discount_code','tax_codes.total_tax_percentage as tax_percentage','discount_codes.discount_percentage as discount_percentage')
                ->where('products.slack', '=', $cart_item_key)
                ->categoryJoin()
                ->supplierJoin()
                ->taxcodeJoin()
                ->discountcodeJoin()
                ->categoryActive()
                ->supplierActive()
                ->taxcodeActive()
                ->quantityCheck($cart_item->quantity)
                ->first();
                if (empty($product_data)) {
                    throw new Exception("Product code: ".$cart_item->product_code." not available currently in stock", 400);
                }

                $sub_total_purchase_price_excluding_tax = $cart_item->quantity*$product_data->purchase_amount_excluding_tax;
                $total_amount = $cart_item->quantity*$product_data->sale_amount_excluding_tax;
                
                $discount_amount = $this->calculate_discount($total_amount, $product_data->discount_percentage);

                $total_amount_after_discount = ($total_amount-$discount_amount);

                $tax_amount = $this->calculate_tax($total_amount_after_discount, $product_data->tax_percentage);
                
                $item_total = ($total_amount_after_discount+$tax_amount);

                if(isset($product_data->tax_code_id)){
                    $product_tax_component_data = TaxcodeTypeModel::select('tax_type', 'tax_percentage')->where("tax_code_id", $product_data->tax_code_id)->get()->toArray();
                    foreach($product_tax_component_data as $key => $product_tax_component_data_item){
                        $tax_component_amount = $this->calculate_tax($total_amount_after_discount, $product_tax_component_data_item['tax_percentage']);
                        $product_tax_component_data[$key]['tax_amount'] = $tax_component_amount;
                    }
                    $product_tax_component_data = json_encode($product_tax_component_data);
                }

                $order_products[] = [
                    'order_id' => 0,
                    'product_slack' => $product_data->slack,
                    'product_id' => $product_data->id,
                    'product_code' => $product_data->product_code,
                    'name' => $product_data->name,
                    
                    'quantity' => $cart_item->quantity,
                    'purchase_amount_excluding_tax' => $product_data->purchase_amount_excluding_tax,
                    'sale_amount_excluding_tax' => $product_data->sale_amount_excluding_tax,
                    
                    'discount_code_id' => isset($product_data->discount_code_id)?$product_data->discount_code_id:NULL,
                    'discount_code' => isset($product_data->discount_code)?$product_data->discount_code:NULL,
                    'discount_percentage' => isset($product_data->discount_percentage)?$product_data->discount_percentage:0,

                    'tax_code_id' => isset($product_data->tax_code_id)?$product_data->tax_code_id:NULL,
                    'tax_code' => isset($product_data->tax_code)?$product_data->tax_code:NULL,
                    'tax_percentage' => $product_data->tax_percentage,
                    'tax_components' => ($product_data->tax_percentage>0)?$product_tax_component_data:NULL,

                    'sub_total_purchase_price_excluding_tax' => $sub_total_purchase_price_excluding_tax,
                    'sub_total_sale_price_excluding_tax' => $total_amount,
                    'discount_amount' => $discount_amount,
                    'total_after_discount' => $total_amount_after_discount,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $item_total,
                ]; 
            }

            $total_purchase_amount_excluding_tax_array = data_get($order_products, '*.sub_total_purchase_price_excluding_tax', 0);
            $total_purchase_amount_excluding_tax = array_sum($total_purchase_amount_excluding_tax_array);

            $total_sale_amount_excluding_tax_array = data_get($order_products, '*.sub_total_sale_price_excluding_tax', 0);
            $total_sale_amount_excluding_tax = array_sum($total_sale_amount_excluding_tax_array);

            $store_level_total_discount_amount = $this->calculate_discount($total_sale_amount_excluding_tax, $store_level_total_discount_percentage);

            $total_discount_amount_array = data_get($order_products, '*.discount_amount', 0);
            $total_discount_amount = array_sum($total_discount_amount_array);
            $product_level_total_discount_amount = $total_discount_amount;
            $total_discount_amount = $total_discount_amount+$store_level_total_discount_amount;

            $total_amount_after_discount = ($total_sale_amount_excluding_tax-$total_discount_amount);

            $store_level_total_tax_amount = $this->calculate_tax($total_amount_after_discount, $store_level_total_tax_percentage);

            $total_tax_amount_array = data_get($order_products, '*.tax_amount', 0);
            $total_tax_amount = array_sum($total_tax_amount_array);
            $product_level_total_tax_amount = $total_tax_amount;
            $total_tax_amount = $total_tax_amount+$store_level_total_tax_amount;

            if(isset($store_data->tax_code_id)){
                $store_tax_component_data = TaxcodeTypeModel::select('tax_type', 'tax_percentage')->where("tax_code_id", $store_data->tax_code_id)->get()->toArray();
                foreach($store_tax_component_data as $key => $store_tax_component_data_item){
                    $tax_component_amount = $this->calculate_tax($total_amount_after_discount, $store_tax_component_data_item['tax_percentage']);
                    $store_tax_component_data[$key]['tax_amount'] = $tax_component_amount;
                }
                $store_tax_component_data = json_encode($store_tax_component_data);
            }
            
            //$total_order_amount_array = data_get($order_products, '*.total_amount', 0);
            //$total_order_amount = array_sum($total_order_amount_array);
            $total_order_amount = ($total_amount_after_discount+$total_tax_amount);
            
            $customer = [
                'customer_number'   => $request->customer_number,
                'customer_email'    => $request->customer_email
            ];
            $customer = $this->handle_customer($customer);
            
            $order = [
                "customer_id" => $customer['customer_id'],
                "customer_phone" => $customer['phone'],
                "customer_email" => $customer['email'],

                "store_level_discount_code_id" => isset($store_data->discount_code_id)?$store_data->discount_code_id:NULL,
                "store_level_discount_code" => isset($store_data->discount_code)?$store_data->discount_code:NULL,
                "store_level_total_discount_percentage" => $store_level_total_discount_percentage,
                "store_level_total_discount_amount" => $store_level_total_discount_amount,
                "product_level_total_discount_amount" => $product_level_total_discount_amount,

                "store_level_tax_code_id" => isset($store_data->tax_code_id)?$store_data->tax_code_id:NULL,
                "store_level_tax_code" => isset($store_data->tax_code)?$store_data->tax_code:NULL,
                "store_level_total_tax_percentage" => $store_level_total_tax_percentage,
                "store_level_total_tax_amount" => $store_level_total_tax_amount,
                'store_level_total_tax_components' => ($store_level_total_tax_percentage>0)?$store_tax_component_data:NULL,
                "product_level_total_tax_amount" => $product_level_total_tax_amount,

                "purchase_amount_subtotal_excluding_tax" => $total_purchase_amount_excluding_tax,
                "sale_amount_subtotal_excluding_tax" => $total_sale_amount_excluding_tax,
                "total_discount_amount" => $total_discount_amount,
                "total_after_discount" => $total_amount_after_discount,
                "total_tax_amount" => $total_tax_amount,
                "total_order_amount" => $total_order_amount,

                'payment_method_id' => $payment_method->id,
                'payment_method_slack' => $payment_method->slack,
                'payment_method' => $payment_method->label,
                'status' => $order_status
            ];

        }

        return [
            'order_data' => $order,
            'order_products_data' => $order_products
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

    private function handle_customer($customer){

        $customer_phone = trim($customer['customer_number']);
        $customer_email = trim($customer['customer_email']);
        
        if($customer_phone != '' || $customer_email != ''){
            $customer_data = CustomerModel::select('id', 'name', 'email', 'phone')
            ->where(function ($query) use ($customer_email, $customer_phone) {
                $query->where('email', '=', $customer_email)
                ->orWhere('phone', '=', $customer_phone);
            })
            ->first();

            if(empty($customer_data)){
                $customer = [
                    'slack'         => $this->generate_slack("customers"),
                    'customer_type' => 'WALKIN',
                    'name'          => '',
                    'email'         => (isset($customer_email) && ($customer_email != '' && $customer_email != null))?$customer_email:'',
                    'phone'         => (isset($customer_phone) && ($customer_phone != '' && $customer_phone != null))?$customer_phone:'',
                    'status'        => 1,
                    "created_by"    => request()->logged_user_id
                ];
                $customer_id = CustomerModel::create($customer)->id;
            }else{
                $customer_id = $customer_data->id;
                $customer = [
                    'name'          => $customer_data->name,
                    'email'         => (isset($customer_email) && ($customer_email != '' && $customer_email != null))?$customer_email:$customer_data->email,
                    'phone'         => (isset($customer_phone) && ($customer_phone != '' && $customer_phone != null))?$customer_phone:$customer_data->phone,
                    'status'        => 1,
                    'updated_by'    => request()->logged_user_id
                ];
    
                $action_response = CustomerModel::where('id', $customer_id)
                ->update($customer);

                CustomerModel::where('id', '!=', $customer_id)
                ->where(function ($query) use ($customer_email, $customer_phone) {
                    $query->where('email', '=', $customer_email)
                    ->orWhere('phone', '=', $customer_phone);
                })->delete();

            }
            $customer_data = CustomerModel::select('id', 'email', 'phone')
            ->where(function ($query) use ($customer_email, $customer_phone) {
                $query->where('email', '=', $customer_email)
                ->orWhere('phone', '=', $customer_phone);
            })
            ->first();
            
        }else{
            $customer_data = CustomerModel::select('id', 'email', 'phone')
            ->where('customer_type', '=', 'DEFAULT')
            ->active()
            ->first();
            $customer_id = $customer_data->id;
        }

        $customer = [
            'customer_id' => $customer_id,
            'email'       => $customer_data->email,
            'phone'       => $customer_data->phone,
        ];
        return $customer;
    }

    public function filter_orders(Request $request){
        try{

            $keyword = $request->keyword;

            $order_list = OrderModel::select("*")
            ->where('order_number', 'like', $keyword.'%')
            ->orWhere('customer_email', 'like', $keyword.'%')
            ->orWhere('customer_phone', 'like', $keyword.'%')
            ->limit(25)
            ->get();
            
            $orders = OrderResource::collection($order_list);
           
            return response()->json($this->generate_response(
                array(
                    "message" => "Order filtered successfully", 
                    "data" => $orders
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
}
