<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\AdsResource;
use App\Models\Ads as AdsModel;

class Ads extends Controller
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
           
            $query = AdsModel::select('ads.*', 'user_created.fullname','category.label_en')
            ->take($limit)
            ->skip($offset)
            ->CategoryJoin()
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
            
            $ads = AdsResource::collection($query);
         
            $total_count = AdsModel::select("id")->get()->count();

            $item_array = [];
            foreach($ads as $key => $ads){
                
                $ads = $ads->toArray($request);
                
                $item_array[$key][] = $ads['title_ar'];
                $item_array[$key][] = $ads['title_en'];
                $item_array[$key][] = $ads['category']['label_en'];
                $item_array[$key][] = $ads['created_at_label'];
                $item_array[$key][] = $ads['updated_at_label'];
                $item_array[$key][] = $ads['created_by']['fullname'];
                $item_array[$key][] = view('ads.layouts.ads_actions', ['ads' => $ads])->render();
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

            if(!check_access(['A_ADD_ADS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

           
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $photo = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/ads');
                $image->move($destinationPath, $photo);
            }
           
           
            DB::beginTransaction();
          
            $ads = [
                "slack" => $this->generate_slack("ads"),
                "photo" => 'uploads/ads/'.$photo,
                "title_ar"=>$request->title_ar,
                "title_en"=>$request->title_en,
                "description_ar"=>$request->description_ar,
                "description_en"=>$request->description_en,
                "category_id"=>$request->category,
                "created_by" => $request->logged_user_id
            ];
            
            $ads_id = AdsModel::create($ads)->id;

           
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Ads created successfully", 
                    "data"    => $ads['slack']
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

            if(!check_access(['A_EDIT_ADS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $ads_data_exists = AdsModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['title_en', '=', trim($request->title_en)],
            ])
            ->first();
            if (!empty($ads_data_exists)) {
                throw new Exception("Ads already exists", 400);
            }
            if ($request->file('photo')) {
                $image = $request->file('photo');
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/ads');
                $image->move($destinationPath, $name);
            }
            DB::beginTransaction();

            $ads = [
                "slack" => $this->generate_slack("ads"),
                "photo" => 'uploads/ads/'.$photo,
                "title_ar"=>$request->title_ar,
                "title_en"=>$request->title_en,
                "description_ar"=>$request->description_ar,
                "description_en"=>$request->description_en,
                "category_id"=>$request->category,
                "created_by" => $request->logged_user_id
            ];

            $action_response = AdsModel::where('slack', $slack)
            ->update($ads);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Ads updated successfully", 
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
            'title_ar' => $this->get_validation_rules("name_title_ar", true),
            'title_en' => $this->get_validation_rules("name_title_en", true),
            'description_ar' => $this->get_validation_rules("name_description_ar", true),
            'description_en' => $this->get_validation_rules("name_description_en", true),
            'category'=>$this->get_validation_rules("name_category", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
