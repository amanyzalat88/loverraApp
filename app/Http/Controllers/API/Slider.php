<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\SliderResource;
use App\Models\Slider as SliderModel;

class Slider extends Controller
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
            
            $query = SliderModel::select('slider.*', 'user_created.fullname')
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

            $sliders = SliderResource::collection($query);
           
            $total_count = SliderModel::select("id")->get()->count();
 
            $item_array = [];
            foreach($sliders as $key => $slider){
                
                $slider = $slider->toArray($request);
                
                $item_array[$key][] = $slider['photo_ar'];
                $item_array[$key][] = $slider['photo_en'];
                
                $item_array[$key][] = $slider['created_at_label'];
                $item_array[$key][] = $slider['updated_at_label'];
                $item_array[$key][] =(isset($slider['created_by']) && $slider['created_by']['fullname'] != '')?$slider['created_by']['fullname']:'-';
                
                $item_array[$key][] = view('slider.layouts.slider_actions', ['slider' => $slider])->render();
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

            if(!check_access(['A_ADD_SLIDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

           
            if ($request->hasFile('photo_ar')) {
                $image = $request->file('photo_ar');
                $photo_ar = time().'1.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/slider');
                $image->move($destinationPath, $photo_ar);
            }
            if ($request->hasFile('photo_en')) {
                $image1 = $request->file('photo_en');
                $photo_en = time().'.'.$image1->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/slider');
                $image1->move($destinationPath, $photo_en);
            }
           
            DB::beginTransaction();
          
            $slider = [
                "slack" => $this->generate_slack("slider"),
                "photo_ar" => 'uploads/slider/'.$photo_ar,
                "photo_en" => 'uploads/slider/'.$photo_en,
                "created_by" => $request->logged_user_id
            ];
            
            $slider_id = SliderModel::create($slider)->id;

           
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Slider Photo created successfully", 
                    "data"    => $slider['slack']
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

            if(!check_access(['A_EDIT_SLIDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

           
            if ($request->hasFile('photo_ar')) {
                $image = $request->file('photo_ar');
                $photo_ar = time().'1.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/slider');
                $image->move($destinationPath, $photo_ar);
            }
            if ($request->hasFile('photo_en')) {
                $image1 = $request->file('photo_en');
                $photo_en = time().'.'.$image1->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/slider');
                $image1->move($destinationPath, $photo_en);
            }
            DB::beginTransaction();

            $slider = [
                "slack" => $this->generate_slack("slider"),
                "photo_ar" => 'uploads/slider/'.$photo_ar,
                "photo_en" => 'uploads/slider/'.$photo_en,
                "created_by" => $request->logged_user_id
            ];

            $action_response = SliderModel::where('slack', $slack)
            ->update($slider);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Slider updated successfully", 
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
    public function destroy($slack)
    {
        SliderModel::where('slack', $slack)->delete();
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'photo_ar' => $this->get_validation_rules("name_label_ar", true),
            'photo_en' => $this->get_validation_rules("name_label_en", true)
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
