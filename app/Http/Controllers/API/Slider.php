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

            $slider = SliderModel::collection($query);
           
            $total_count = SliderModel::select("id")->get()->count();

            $item_array = [];
            foreach($slider as $key => $photo){
                
                $photo = $photo->toArray($request);
                
                $item_array[$key][] = $photo['photo_ar'];
                $item_array[$key][] = $photo['photo_en'];
                
                $item_array[$key][] = $photo['created_at_label'];
                $item_array[$key][] = $photo['updated_at_label'];
                $item_array[$key][] = $photo['created_by']['fullname'];
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
                $photo_ar = time().'.'.$image->getClientOriginalExtension();
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

            if(!check_access(['A_EDIT_CATEGORY'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $category_data_exists = CategoryModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label_en', '=', trim($request->category_name_en)],
            ])
            ->first();
            if (!empty($category_data_exists)) {
                throw new Exception("Category already exists", 400);
            }
            if ($request->file('photo')) {
                $image = $request->file('photo');
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/category');
                $image->move($destinationPath, $name);
            }
            DB::beginTransaction();

            $category = [
                "label_en" => Str::title($request->category_name_en),
                "description_en" => $request->description_en,
                "label_ar" => Str::title($request->category_name_ar),
                "description_ar" => $request->description_ar,
                "status" => $request->status,
                "photo"=> "uploads/category/".$name,
                "parent" =>  $request->parent,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = CategoryModel::where('slack', $slack)
            ->update($category);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Category updated successfully", 
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
            'photo_ar' => $this->get_validation_rules("name_label_ar", true),
            'photo_en' => $this->get_validation_rules("name_label_en", true)
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
