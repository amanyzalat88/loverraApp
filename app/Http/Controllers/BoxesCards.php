<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BoxesCard as BoxesCardsModel;
use App\Http\Resources\BoxesCardResource;

class BoxesCards extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXESCARDS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('boxes_card.boxes', $data);
    }

    //This is the function that loads the add/edit page
    public function add_boxes($slack = null){
        //check access
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXESCARDS';
        $data['action_key'] = ($slack == null)?'A_ADD_BOXESCARDS':'A_EDIT_BOXESCARDS';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();
        
        
      
        $data['boxes_data'] = null;
        if(isset($slack)){
            $boxes =BoxesCardsModel::where('slack', '=', $slack)->first();
            if (empty($boxes)) {
                abort(404);
            }
            
            $boxes_data = new BoxesCardResource($boxes);
            $data['boxes_data'] = $boxes_data;
        }
        
         
        return view('boxes_card.add_boxes', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_GIFTS';
        $data['sub_menu_key'] = 'SM_BOXESCARDS';
        $data['action_key'] = 'A_DETAIL_BOXESCARDS';
        check_access([$data['action_key']]);

        $boxes = BoxesCardsModel::where('slack', '=', $slack)->first();
        
        if (empty($boxes)) {
            abort(404);
        }

        $boxes_data = new BoxesCardResource($boxes);
        
        $data['boxes_data'] = $boxes_data;

        return view('boxes_card.boxes_detail', $data);
    }
}
