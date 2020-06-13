<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Product as ProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\Discountcode as DiscountcodeModel;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\ProductResource;

class Product extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_PRODUCT';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('product.products', $data);
    }

    //This is the function that loads the add/edit page
    public function add_product($slack = null){
        //check access
        $data['menu_key'] = 'MM_PRODUCT';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        $data['action_key'] = ($slack == null)?'A_ADD_PRODUCT':'A_EDIT_PRODUCT';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('PRODUCT_STATUS')->active()->sortValueAsc()->get();

        $data['suppliers'] = SupplierModel::select('slack', 'supplier_code', 'name')->sortNameAsc()->active()->get();

        $data['categories'] = CategoryModel::select('slack', 'category_code', 'label')->sortLabelAsc()->active()->get();

        $data['taxcodes'] = TaxcodeModel::select('slack', 'tax_code', 'label')->sortLabelAsc()->active()->get();

        $data['discount_codes'] = DiscountcodeModel::select('slack', 'discount_code', 'label')->sortLabelAsc()->active()->get();

        $data['product_data'] = null;
        if(isset($slack)){
            
            $product = ProductModel::where('products.slack', '=', $slack)->first();
            if (empty($product)) {
                abort(404);
            }
            
            $product_data = new ProductResource($product);

            $data['product_data'] = $product_data;
        }

        return view('product.add_product', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_PRODUCT';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        $data['action_key'] = 'A_DETAIL_PRODUCT';
        check_access([$data['action_key']]);

        $product = ProductModel::where('products.slack', '=', $slack)->first();
        
        if (empty($product)) {
            abort(404);
        }

        $product_data = new ProductResource($product);
        
        $data['product_data'] = $product_data;

        return view('product.product_detail', $data);
    }

    //This is the function that loads the barcode generate page
    public function generate_barcode(){
        $data['menu_key'] = 'MM_PRODUCT';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        $data['action_key'] = 'A_GENERATE_BARCODE_PRODUCT';
        check_access([$data['action_key']]);

        return view('product.product_barcode', $data);        
    }
}
