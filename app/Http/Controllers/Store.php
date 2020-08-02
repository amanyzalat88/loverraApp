<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Store as StoreModel;
use App\Models\UserStore as UserStoreModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\MasterInvoicePrintType as MasterInvoicePrintTypeModel;
use App\Models\Country as CountryModel;

use App\Http\Resources\StoreResource;

class Store extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_STORE';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('store.stores', $data);
    }

    //This is the function that loads the add/edit page
    public function add_store($slack = null){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_STORE';
        $data['action_key'] = ($slack == null)?'A_ADD_STORE':'A_EDIT_STORE';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('STORE_STATUS')->active()->sortValueAsc()->get();

        $data['invoice_print_types'] = MasterInvoicePrintTypeModel::select('print_type_value', 'print_type_label')->active()->get();

        $data['currency_list'] = CountryModel::select('currency_code', 'currency_name')
        ->where('currency_code', '!=', '')
        ->whereNotNull('currency_code')
        ->active()
        ->groupBy('currency_code')
        ->get();

        $data['store_data'] = null;
        $data['tax_codes'] = null;
        $data['discount_codes'] = null;
        
        if(isset($slack)){
            
            $store = StoreModel::where('slack', '=', $slack)->first();
            if (empty($store)) {
                abort(404);
            }

            $store_data = new StoreResource($store);
            $data['store_data'] = $store_data;
            
            $data['tax_codes'] = TaxcodeModel::withoutGlobalScopes()->select('slack', 'tax_code', 'label')->where('store_id', $store_data->id)->active()->sortLabelAsc()->get();
        
            $data['discount_codes'] = DiscountcodeModel::withoutGlobalScopes()->select('slack', 'discount_code', 'label')->where('store_id', $store_data->id)->active()->sortLabelAsc()->get();
        }

        return view('store.add_store', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_STORE';
        $data['action_key'] = 'A_DETAIL_STORE';
        check_access([$data['action_key']]);

        $store = StoreModel::where('slack', '=', $slack)->first();
        
        if (empty($store)) {
            abort(404);
        }

        $store_data = new StoreResource($store);
        
        $data['store_data'] = $store_data;

        return view('store.store_detail', $data);
    }

    public function select_store(Request $request){
        $user_id = $request->logged_user_id;

        if ($request->is_super_admin == true) {
            $data['stores'] = StoreModel::select('stores.slack as store_slack','stores.store_code', 'stores.name', 'stores.address')->active()->get();
        }else{
            $data['stores'] = UserStoreModel::select('stores.slack as store_slack','stores.store_code', 'stores.name', 'stores.address')
            ->where('user_stores.user_id','=',$user_id)
            ->storeData()
            ->get();
        }
        $data['is_super_admin'] = $request->is_super_admin;

        return view('store.select_store', $data);
    }
}
