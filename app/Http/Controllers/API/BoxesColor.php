<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\BoxesColorResource;
use App\Models\BoxesColor as BoxesColorModel;

class BoxesColor extends Controller
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
           
            $query = BoxesColorModel::select('boxes_color.*', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
           // ->statusJoin()
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

            $boxes = BoxesColorResource::collection($query);
           
            $total_count = BoxesColorModel::select("id")->get()->count();

            $item_array = [];
            foreach($boxes as $key => $boxes){
                
                $boxes = $boxes->toArray($request);
                
                $item_array[$key][] = $boxes['name_ar'];
                $item_array[$key][] = $boxes['name_en'];
                $item_array[$key][] = $boxes['price'];
             
               
                $item_array[$key][] = $boxes['created_at_label'];
                $item_array[$key][] = $boxes['updated_at_label'];
                $item_array[$key][] = $boxes['created_by']['fullname'];
                $item_array[$key][] = view('boxes_color.layouts.boxes_actions', ['boxes' => $boxes])->render();
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

            if(!check_access(['A_ADD_BOXESCOLOR'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

           
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $photo = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/gifts/colors');
                $image->move($destinationPath, $photo);
            }
           
           
            DB::beginTransaction();
          
            $boxes = [
                "slack" => $this->generate_slack("boxes"),
                "photo" => 'uploads/gifts/colors/'.$photo,
                "name_ar"=>$request->name_ar,
                "name_en"=>$request->name_en,
                "price"=>$request->price,
                'box_id'=>$request->box_id,
                "created_by" => $request->logged_user_id
            ];
            
            $boxes_id = BoxesColorModel::create($boxes)->id;

           
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Boxes Color   created successfully", 
                    "data"    => $boxes['slack']
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

            if(!check_access(['A_EDIT_BOXESCOLOR'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $boxes_data_exists = BoxesColorModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['name_en', '=', trim($request->name_en)],
            ])
            ->first();
            if (!empty($boxes_data_exists)) {
                throw new Exception("Boxes Color already exists", 400);
            }
            if ($request->file('photo')) {
                $image = $request->file('photo');
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/gifts/colors');
                $image->move($destinationPath, $name);
            }
            DB::beginTransaction();

            $boxes = [
                "slack" => $this->generate_slack("boxes"),
                "photo" => 'uploads/gifts/colors/'.$photo,
                "name_ar"=>$request->name_ar,
                "name_en"=>$request->name_en,
                "price"=>$request->price,
                'box_id'=>$request->box_id,
                "created_by" => $request->logged_user_id
            ];

            $action_response = BoxesColorModel::where('slack', $slack)
            ->update($boxes);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Boxes Color updated successfully", 
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
            'name_ar' => $this->get_validation_rules("name_name_ar", true),
            'name_en' => $this->get_validation_rules("name_name_en", true),
            'price' => $this->get_validation_rules("name_price", true),
           
        
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
