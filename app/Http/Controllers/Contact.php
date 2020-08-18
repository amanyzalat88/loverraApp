<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Contact as ContactModel;
use Illuminate\Support\Facades\DB;
 
use App\Http\Resources\ContactResource;

class Contact extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_CONTACT';
        $data['sub_menu_key'] = 'SM_CONTACT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('contact.contact', $data);
    }

    //This is the function that loads the add/edit page
    public function add_ads($slack = null){
        //check access
        $data['menu_key'] = 'MM_ADS';
        $data['sub_menu_key'] = 'SM_ADS';
        $data['action_key'] = ($slack == null)?'A_ADD_ADS':'A_EDIT_ADS';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();
        $data['categories'] = CategoryModel::select('id as value', 'label_en')->sortLabelAsc()->active()->get();
        
      
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
        $data['menu_key'] = 'MM_CONTACT';
        $data['sub_menu_key'] = 'SM_CONTACT';
        $data['action_key'] = 'A_DETAIL_CONTACT';
        check_access([$data['action_key']]);

        $contact = ContactModel::where('slack', '=', $slack)->first();
        
        if (empty($contact)) {
            abort(404);
        }

        $contact_data = new ContactResource($contact);
        
        $data['contact_data'] = $contact_data;

        return view('contact.contact_detail', $data);
    }
}
