<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Models\Boxes as BoxesModel;
use App\Http\Resources\BoxesResource;

class Boxes extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('boxes.boxes', $data);
    }

    //This is the function that loads the add/edit page
    public function add_boxes($slack = null){
        //check access
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXES';
        $data['action_key'] = ($slack == null)?'A_ADD_BOXES':'A_EDIT_BOXES';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();
       
        
      
        $data['boxes_data'] = null;
        if(isset($slack)){
            $boxes =BoxesModel::where('slack', '=', $slack)->first();
            if (empty($boxes)) {
                abort(404);
            }
            
            $boxes_data = new BoxesResource($boxes);
            $data['boxes_data'] = $boxes_data;
        }
        
         
        return view('boxes.add_boxes', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXES';
        $data['action_key'] = 'A_DETAIL_BOXES';
        check_access([$data['action_key']]);

        $boxes = BoxesModel::where('slack', '=', $slack)->first();
        
        if (empty($boxes)) {
            abort(404);
        }

        $boxes_data = new BoxesResource($boxes);
        
        $data['boxes_data'] = $boxes_data;

        return view('boxes.boxes_detail', $data);
    }
}
