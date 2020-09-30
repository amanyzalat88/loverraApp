<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Ads as AdsModel;
use Illuminate\Support\Facades\DB;
use App\Models\Category as CategoryModel;
use App\Http\Resources\AdsResource;

class Ads extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ADS';
        $data['sub_menu_key'] = 'SM_ADS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('ads.ads', $data);
    }

    //This is the function that loads the add/edit page
    public function add_ads($slack = null){
        //check access
        $data['menu_key'] = 'MM_ADS';
        $data['sub_menu_key'] = 'SM_ADS';
        $data['action_key'] = ($slack == null)?'A_ADD_ADS':'A_EDIT_ADS';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();
        $data['categories'] = CategoryModel::select('id as value', 'label_en')->whereIn('parent_id',0)->sortLabelAsc()->active()->get();
        //whereIn('parent_id',[7,13,15,12])
        
      
        $data['ads_data'] = null;
        if(isset($slack)){
            $ads = AdsModel::where('slack', '=', $slack)->first();
            if (empty($ads)) {
                abort(404);
            }
            
            $ads_data = new AdsResource($ads);
            $data['ads_data'] = $ads_data;
        }
        
         
        return view('ads.add_ads', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ADS';
        $data['sub_menu_key'] = 'SM_ADS';
        $data['action_key'] = 'A_DETAIL_ADS';
        check_access([$data['action_key']]);

        $ads = AdsModel::where('slack', '=', $slack)->first();
        
        if (empty($ads)) {
            abort(404);
        }

        $ads_data = new AdsResource($ads);
        
        $data['ads_data'] = $ads_data;

        return view('ads.ads_detail', $data);
    }
}
