<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role as RoleModel;

use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\Discountcode as DiscountcodeModel;

class Report extends Controller
{
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        check_access(array($data['menu_key']));

        //user
        $data['user_statuses'] = MasterStatus::select('value', 'label')->filterByKey('USER_STATUS')->active()->sortValueAsc()->get();

        $data['roles'] = RoleModel::select('slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->sortLabelAsc()->get();

        //product
        $data['product_statuses'] = MasterStatus::select('value', 'label')->filterByKey('PRODUCT_STATUS')->active()->sortValueAsc()->get();

        $data['suppliers'] = SupplierModel::select('slack', 'supplier_code', 'name')->sortNameAsc()->get();

        $data['categories'] = CategoryModel::select('slack', 'category_code', 'label')->sortLabelAsc()->get();

        $data['taxcodes'] = TaxcodeModel::select('slack', 'tax_code', 'label')->sortLabelAsc()->get();

        $data['discountcodes'] = DiscountcodeModel::select('slack', 'discount_code', 'label')->sortLabelAsc()->get();

        //order
        $data['order_statuses'] = MasterStatus::select('value', 'label')->filterByKey('ORDER_STATUS')->active()->sortValueAsc()->get();

        //purchase order
        $data['purchase_order_statuses'] = MasterStatus::select('value', 'label')->filterByKey('PURCHASE_ORDER_STATUS')->active()->sortValueAsc()->get();

        //store
        $data['store_statuses'] = MasterStatus::select('value', 'label')->filterByKey('STORE_STATUS')->active()->sortValueAsc()->get();

        //customer
        $data['customer_statuses'] = MasterStatus::select('value', 'label')->filterByKey('CUSTOMER_STATUS')->active()->sortValueAsc()->get();

        //category
        $data['category_statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();

        //supplier
        $data['supplier_statuses'] = MasterStatus::select('value', 'label')->filterByKey('SUPPLIER_STATUS')->active()->sortValueAsc()->get();

        //tax code
        $data['taxcode_statuses'] = MasterStatus::select('value', 'label')->filterByKey('TAX_CODE_STATUS')->active()->sortValueAsc()->get();

        //discount code
        $data['discountcode_statuses'] = MasterStatus::select('value', 'label')->filterByKey('DISCOUNTCODE_STATUS')->active()->sortValueAsc()->get();

        return view('report.report', $data);
    }
}
