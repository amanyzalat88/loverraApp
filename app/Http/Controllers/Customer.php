<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer as CustomerModel;
use App\Models\CustomerAddress as CustomerAddressModel;
use App\Models\MasterStatus;

use App\Http\Resources\CustomerResource;

class Customer extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_CUSTOMERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('customer.customers', $data);
    }

    //This is the function that loads the add/edit page
    public function add_customer($slack = null){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_CUSTOMERS';
        $data['action_key'] = ($slack == null)?'A_ADD_CUSTOMER':'A_EDIT_CUSTOMER';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CUSTOMER_STATUS')->active()->sortValueAsc()->get();

        $data['customer_data'] = null;
        $data['customer_data']['address'] = null;
        if(isset($slack)){
            $customer = CustomerModel::where('customers.slack', $slack)
            ->first();

            if (empty($customer)) {
                abort(404);
            }
            $customer_data = new CustomerResource($customer);
            $customer_address = CustomerAddressModel::where('customer_id', $customer_data->id)
            ->first();
           
            $data['customer_data'] = $customer_data;
            $data['customer_data']['address']=$customer_address;
        }

        return view('customer.add_customer', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_CUSTOMERS';
        $data['action_key'] = 'A_DETAIL_CUSTOMER';
        check_access([$data['action_key']]);

        $customer = CustomerModel::where('slack', $slack)
        ->first();
        
        if (empty($customer)) {
            abort(404);
        }

        $customer_data = new CustomerResource($customer);
        $customer_address_array = collect($customer_data->addresses)->toArray();
        $customer_data = collect($customer_data);
        $customer_data = $customer_data->all();
        
        $data['customer_data'] = $customer_data;

        return view('customer.customer_detail', $data);
    }
}
