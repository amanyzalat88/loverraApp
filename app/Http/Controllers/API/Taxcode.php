<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\TaxcodeResource;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;

class Taxcode extends Controller
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
            
            $query = TaxcodeModel::select('tax_codes.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $tax_codes = TaxcodeResource::collection($query);
           
            $total_count = TaxcodeModel::select("id")->get()->count();

            $item_array = [];
            foreach($tax_codes as $key => $tax_code){

                $tax_code = $tax_code->toArray($request);

                $item_array[$key][] = $tax_code['label'];
                $item_array[$key][] = $tax_code['tax_code'];
                $item_array[$key][] = $tax_code['total_tax_percentage'];
                $item_array[$key][] = view('common.status', ['status_data' => ['label' => $tax_code['status']['label'], "color" => $tax_code['status']['color']]])->render();
                $item_array[$key][] = $tax_code['created_at_label'];
                $item_array[$key][] = $tax_code['updated_at_label'];
                $item_array[$key][] = (isset($tax_code['created_by']) && $tax_code['created_by']['fullname'] != '')?$tax_code['created_by']['fullname']:'-';
                $item_array[$key][] = view('tax_code.layouts.tax_code_actions', array('tax_code' => $tax_code))->render();
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

            if(!check_access(['A_ADD_TAXCODE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $taxcode_exists = TaxcodeModel::select('id')
            ->where('tax_code', '=', trim($request->tax_code))
            ->first();
            if (!empty($taxcode_exists)) {
                throw new Exception("Tax code already exists", 400);
            }

            DB::beginTransaction();
            
            $taxcode = [
                "slack" => $this->generate_slack("tax_codes"),
                "store_id" => $request->logged_user_store_id,
                "label" => $request->tax_code_name,
                "tax_code" => strtoupper($request->tax_code),
                "total_tax_percentage" => 0,
                "description" => $request->description,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $taxcode_id = TaxcodeModel::create($taxcode)->id;

            $tax_types = json_decode($request->tax_types);
            if(!empty($tax_types)){
                foreach($tax_types as $tax_type){
                    $tax_type_data = [
                        'tax_code_id' => $taxcode_id,
                        'tax_type' => $tax_type->tax_type,
                        'tax_percentage' => $tax_type->tax_percentage,
                        'created_by' => $request->logged_user_id,
                    ];
                    TaxcodeTypeModel::create($tax_type_data)->id;
                }
                $total_percentage_array = data_get($tax_types, '*.tax_percentage', 0);
                $total_percentage = array_sum($total_percentage_array);
            }

            $action_response = TaxcodeModel::where('id', $taxcode_id)
            ->update([
                'total_tax_percentage' => $total_percentage
            ]);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Tax code created successfully", 
                    "data"    => $taxcode['slack']
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

            if(!check_access(['A_EDIT_TAXCODE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $taxcode_exists = TaxcodeModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['tax_code', '=', trim($request->tax_code)],
            ])
            ->first();
            if (!empty($taxcode_exists)) {
                throw new Exception("Tax code already exists", 400);
            }

            DB::beginTransaction();
            
            $taxcode = [
                "label" => $request->tax_code_name,
                "tax_code" => strtoupper($request->tax_code),
                "total_tax_percentage" => 0,
                "description" => $request->description,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];
            
            $action_response = TaxcodeModel::where('slack', $slack)
            ->update($taxcode);

            $taxcode_details = TaxcodeModel::select('id')
            ->where([
                ['slack', '=', $slack]
            ])
            ->first();

            $tax_types = json_decode($request->tax_types);
            if(!empty($tax_types)){

                TaxcodeTypeModel::where('tax_code_id', $taxcode_details->id)->delete();

                foreach($tax_types as $tax_type){
                    $tax_type_data = [
                        'tax_code_id' => $taxcode_details->id,
                        'tax_type' => $tax_type->tax_type,
                        'tax_percentage' => $tax_type->tax_percentage,
                        'created_by' => $request->logged_user_id,
                    ];
                    TaxcodeTypeModel::create($tax_type_data)->id;
                }
                $total_percentage_array = data_get($tax_types, '*.tax_percentage', 0);
                $total_percentage = array_sum($total_percentage_array);
            }

            $action_response = TaxcodeModel::where('id', $taxcode_details->id)
            ->update([
                'total_tax_percentage' => $total_percentage
            ]);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Tax code updated successfully", 
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
            'tax_code_name' => $this->get_validation_rules("name_label", true),
            'tax_code' => $this->get_validation_rules("codes", true),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
