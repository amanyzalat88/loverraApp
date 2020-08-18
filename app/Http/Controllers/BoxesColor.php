<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Boxes as BoxesModel;
use Illuminate\Support\Facades\DB;
use App\Models\BoxesColor as BoxesColorModel;
use App\Http\Resources\BoxesColorResource;

class BoxesColor extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXESCOLOR';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('boxes_color.boxes', $data);
    }

    //This is the function that loads the add/edit page
    public function add_boxes($slack = null){
        //check access
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXESCOLOR';
        $data['action_key'] = ($slack == null)?'A_ADD_BOXESCOLOR':'A_EDIT_BOXESCOLOR';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();
        $data['boxes'] = BoxesModel::select('id as value', 'name_en')->sortLabelAsc()->active()->get();
        
      
        $data['boxes_data'] = null;
        if(isset($slack)){
            $boxes =BoxesColorModel::where('slack', '=', $slack)->first();
            if (empty($boxes)) {
                abort(404);
            }
            
            $boxes_data = new BoxesColorResource($boxes);
            $data['boxes_data'] = $boxes_data;
        }
        
         
        return view('boxes_color.add_boxes', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXESCOLOR';
        $data['action_key'] = 'A_DETAIL_BOXESCOLOR';
        check_access([$data['action_key']]);

        $boxes = BoxesColorModel::where('slack', '=', $slack)->first();
        
        if (empty($boxes)) {
            abort(404);
        }

        $boxes_data = new BoxesColorResource($boxes);
        
        $data['boxes_data'] = $boxes_data;

        return view('boxes_color.boxes_detail', $data);
    }
}
