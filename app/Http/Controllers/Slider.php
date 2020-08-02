<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Slider as SliderModel;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\SliderResource;

class Slider extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SLIDER';
        $data['sub_menu_key'] = 'SM_SLIDER';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('slider.sliders', $data);
    }

    //This is the function that loads the add/edit page
    public function add_slider($slack = null){
        //check access
        $data['menu_key'] = 'MM_SLIDER';
        $data['sub_menu_key'] = 'SM_SLIDER';
        $data['action_key'] = ($slack == null)?'A_ADD_SLIDER':'A_EDIT_SLIDER';
        check_access(array($data['action_key']));

       // $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();

        
      
        $data['slider_data'] = null;
        if(isset($slack)){
            $slider = sliderModel::where('slack', '=', $slack)->first();
            if (empty($slider)) {
                abort(404);
            }
            
            $slider_data = new SliderResource($slider);
            $data['slider_data'] = $slider_data;
        }
        
         
        return view('slider.add_slider', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SLIDER';
        $data['sub_menu_key'] = 'SM_SLIDER';
        $data['action_key'] = 'A_DETAIL_SLIDER';
        check_access([$data['action_key']]);

        $slider = SliderModel::where('slack', '=', $slack)->first();
        
        if (empty($slider)) {
            abort(404);
        }

        $slider_data = new SliderResource($category);
        
        $data['slider_data'] = $slider_data;

        return view('slider.slider_detail', $data);
    }
}
