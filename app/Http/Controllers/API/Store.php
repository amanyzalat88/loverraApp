<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\StoreResource;
use App\Models\Store as StoreModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\Country as CountryModel;

class Store extends Controller
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
            
            $query = StoreModel::select('stores.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $stores = StoreResource::collection($query);
           
            $total_count = StoreModel::select("id")->get()->count();

            $item_array = [];
            foreach($stores as $key => $store){

                $store = $store->toArray($request);

                $item_array[$key][] = $store['store_code'];
                $item_array[$key][] = $store['name'];
                $item_array[$key][] = view('common.status', ['status_data' => ['label' => $store['status']['label'], "color" => $store['status']['color']]])->render();
                $item_array[$key][] = $store['created_at_label'];
                $item_array[$key][] = $store['updated_at_label'];
                $item_array[$key][] = (isset($store['created_by']) && $store['created_by']['fullname'] != '')?$store['created_by']['fullname']:'-';
                $item_array[$key][] = view('store.layouts.store_actions', array('store' => $store))->render();
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

            if(!check_access(['A_ADD_STORE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $store_data_exists = StoreModel::select('id')
            ->where('store_code', '=', trim($request->store_code))
            ->first();
            if (!empty($store_data_exists)) {
                throw new Exception("Store code already assigned to a store", 400);
            }

            $currency_data = CountryModel::select('currency_code', 'currency_name')
            ->where('currency_code', '=', trim($request->currency_code))
            ->active()
            ->first();
            if (empty($currency_data)) {
                throw new Exception("Invalid currency selected", 400);
            }

            DB::beginTransaction();
            
            $store = [
                "slack" => $this->generate_slack("stores"),
                "store_code" => strtoupper(trim($request->store_code)),
                "name" => $request->name,
                "tax_number" => $request->tax_number,
                "address" => $request->address,
                "pincode" => $request->pincode,
                "primary_contact" => $request->primary_contact,
                "secondary_contact" => $request->secondary_contact,
                "primary_email" => $request->primary_email,
                "secondary_email" => $request->secondary_email,
                "invoice_type" => $request->invoice_type,
                "currency_code" => $currency_data->currency_code,
                "currency_name" => $currency_data->currency_name,
                "status" => $request->status,
                "shipping" => $request->shipping,
                "free_shipping" => $request->free_shipping,
                "created_by" => $request->logged_user_id
            ];
            
            $store_id = StoreModel::create($store)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Store created successfully", 
                    "data"    => $store['slack']
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

            if(!check_access(['A_EDIT_STORE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $store_data_exists = StoreModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['store_code', '=', trim($request->store_code)]
            ])
            ->first();
            if (!empty($store_data_exists)) {
                throw new Exception("Store code already assigned to a store", 400);
            }

            $tax_code_id = NULL;
            if(isset($request->tax_code)){
                $taxcode_data = TaxcodeModel::select('id')
                ->where('slack', '=', trim($request->tax_code))
                ->active()
                ->first();
                if (empty($taxcode_data)) {
                    throw new Exception("Tax code not found or inactive in the system", 400);
                }
                $tax_code_id = $taxcode_data->id;
            }
            
            $discount_code_id = NULL;
            if(isset($request->discount_code)){
                $discount_code_data = DiscountcodeModel::select('id')
                ->where('slack', '=', trim($request->discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
                $discount_code_id = $discount_code_data->id;
            }

            $currency_data = CountryModel::select('currency_code', 'currency_name')
            ->where('currency_code', '=', trim($request->currency_code))
            ->active()
            ->first();
            if (empty($currency_data)) {
                throw new Exception("Invalid currency selected", 400);
            }

            if($request->status == 0){
                $active_store_exists = StoreModel::select('id')
                ->where([
                    ['slack', '!=', $slack],
                    ['status', '=', 1]
                ])
                ->count();
                if ($active_store_exists == 0) {
                    throw new Exception("Atleast one store needs to be active in the system", 400);
                }
            }

            DB::beginTransaction();

            $store = [
                "store_code" => strtoupper(trim($request->store_code)),
                "name" => $request->name,
                "tax_number" => $request->tax_number,
                "tax_code_id" => $tax_code_id,
                "discount_code_id" => $discount_code_id,
                "address" => $request->address,
                "pincode" => $request->pincode,
                "primary_contact" => $request->primary_contact,
                "secondary_contact" => $request->secondary_contact,
                "primary_email" => $request->primary_email,
                "secondary_email" => $request->secondary_email,
                "invoice_type" => $request->invoice_type,
                "currency_code" => $currency_data->currency_code,
                "currency_name" => $currency_data->currency_name,
                "status" => $request->status,
                "shipping" => $request->shipping,
                "free_shipping" => $request->free_shipping,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = StoreModel::where('slack', $slack)
            ->update($store);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Store updated successfully", 
                    "data"    => $slack
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

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => $this->get_validation_rules("name_label", true),
            'address' => $this->get_validation_rules("text", true),
            'pincode' => $this->get_validation_rules("pincode", false),
            'store_code' => $this->get_validation_rules("codes", true),
            'tax_number' => $this->get_validation_rules("name_label", false),
            'primary_contact' => $this->get_validation_rules("phone", false),
            'secondary_contact' => $this->get_validation_rules("phone", false),
            'primary_email' => $this->get_validation_rules("email", false),
            'secondary_email' => $this->get_validation_rules("email", false),
            'invoice_type' => 'max:50|required',
            'status' => $this->get_validation_rules("status", true),
            "shipping" =>'required',
            "free_shipping" =>'required',
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
