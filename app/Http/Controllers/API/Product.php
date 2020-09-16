<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\ProductResource;
use App\Models\Product as ProductModel;
use App\Models\Photos as PhotosModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\MasterStatus;

use Mpdf\Mpdf;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class Product extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
    
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column = $request->columns[$order_by]['name'];

            $filter_string =null;// $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
           
            $query = ProductModel::select('products.*', 'master_status.label as status_label', 'master_status.color as status_color', 'suppliers.name as supplier_name', 'suppliers.status as supplier_status', 'category.label_en', 'category.status as category_status', 'discount_codes.discount_code as discount_code_label', 'discount_codes.status as discount_code_status', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->statusJoin()
            ->categoryJoin()
            ->supplierJoin()
            
            ->discountcodeJoin()
            ->createdUser()
           
            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->get();
            
            $products = ProductResource::collection($query);
           
            $total_count = ProductModel::select("id")->get()->count();
             
            $item_array = [];
            foreach($products as $key => $product){
                
                $product = $product->toArray($request);

                $item_array[$key][] = $product['product_code'];
                $item_array[$key][] = Str::limit($product['name_en'], 50);
                $item_array[$key][] = Str::limit($product['name_ar'], 50);
               
                $item_array[$key][] = view('common.status_indicators', ['status' => $product['category']['status']])->render().Str::limit($product['category']['label_en'], 50);
               
               // $item_array[$key][] = ($product['discount_code_id'] != null)?(view('common.status_indicators', ['status' => $product['discount_code']['status']])->render().Str::limit($product['discount_code']['label'], 50))." (".$product['discount_code']['discount_code'].")":'-';
                $item_array[$key][] = $product['quantity'];
                $item_array[$key][] = $product['sale_amount_excluding_tax']!=''?$product['sale_amount_excluding_tax']:$product['product_amount_excluding_tax'];
               // $item_array[$key][] = view('common.status', ['status_data' => ['label' => $product['status']['label'], "color" => $product['status']['color']]])->render();
                //$item_array[$key][] = $product['created_at_label'];
               // $item_array[$key][] = $product['updated_at_label'];
               // $item_array[$key][] = (isset($product['created_by']) && $product['created_by']['fullname'] != '')?$product['created_by']['fullname']:'-';
              
                $item_array[$key][] = view('product.layouts.product_actions', array('product' => $product))->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_PRODUCT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $product_data_exists = ProductModel::select('id')
            ->where('product_code', '=', trim($request->product_code))
             
            ->first();
            if (!empty($product_data_exists)) {
                throw new Exception("Product code already assigned to a product", 400);
            }

            $supplier_data = SupplierModel::select('id')
            ->where('slack', '=', trim($request->supplier))
            //->active()
            ->first();
            if (empty($supplier_data)) {
                throw new Exception("Supplier not found or inactive in the system", 400);
            }

            $category_data = CategoryModel::select('id')
            ->where('slack', '=', trim($request->category))
            //->active()
            ->first();
            if (empty($category_data)) {
                throw new Exception("Category not found or inactive in the system", 400);
            }

            $taxcode_data = TaxcodeModel::select('id')
            ->where('slack', '=', trim($request->tax_code))
            //->active()
            ->first();
            if (empty($taxcode_data)) {
                throw new Exception("Tax code not found or inactive in the system", 400);
            }

            $discount_code_id = NULL;
            if(isset($request->discount_code)){
                $discount_code_data = DiscountcodeModel::select('id')
                ->where('slack', '=', trim($request->discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
                $discount_code_id = $discount_code_data->id;
            }
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/product');
                $image->move($destinationPath, $name);
            }
            
            DB::beginTransaction();
			$sale=0.00;
            if($request->sale_price)
				$sale=$request->sale_price;
				
            $product = [
                "slack" => $this->generate_slack("products"),
                "store_id" => $request->logged_user_store_id,
                "name_ar" => $request->product_name_ar,
                "name_en" => $request->product_name_en,
                "soldout" => $request->soldout,
                "product_code" => strtoupper($request->product_code),
                "description_ar" => $request->description_ar,
                "description_en" => $request->description_en,
                "category_id" => $category_data->id,
                "supplier_id" => $supplier_data->id,
                "tax_code_id" => $taxcode_data->id,
                "discount_code_id" => $discount_code_id,
                "quantity" => $request->quantity,
                "photo"=> "uploads/product/".$name,
                "purchase_amount_excluding_tax" => $request->purchase_price,
                "sale_amount_excluding_tax" => $sale,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $product_id = ProductModel::create($product)->id;
            if ($request->hasFile('photos')) {
                $files =$request->file('photos');
                foreach($files as $file) {
                    $name = time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/product');
                    $file->move($destinationPath, $name);
                    $photo = [
                        "product_id" => $product_id,
                        "photo" =>"uploads/product/".$name
                    ];
                    PhotosModel::create($photo);
                } 
            }
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product created successfully", 
                    "data"    => $product['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_PRODUCT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request_update($request);

            $product_data_exists = ProductModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['product_code', '=', trim($request->product_code)],
            ])
            ->first();

            $product_id = ProductModel::select('id')->where('slack',  $slack)->first()->id;
            
            if (!empty($product_data_exists)) {
                throw new Exception("Product code already assigned to a product", 400);
            }

            $supplier_data = SupplierModel::select('id')
            ->where('slack', '=', trim($request->supplier))
            //->active()
            ->first();
            if (empty($supplier_data)) {
                throw new Exception("Supplier not found or inactive in the system", 400);
            }

            $category_data = CategoryModel::select('id')
            ->where('slack', '=', trim($request->category))
            //->active()
            ->first();
            if (empty($category_data)) {
                throw new Exception("Category not found or inactive in the system", 400);
            }

            $taxcode_data = TaxcodeModel::select('id')
            ->where('slack', '=', trim($request->tax_code))
            //->active()
            ->first();
            if (empty($taxcode_data)) {
                throw new Exception("Taxcode not found or inactive in the system", 400);
            }

            $discount_code_id = NULL;
            if(isset($request->discount_code)){
                $discount_code_data = DiscountcodeModel::select('id')
                ->where('slack', '=', trim($request->discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
                $discount_code_id = $discount_code_data->id;
            }
            if ($request->file('photo')) {
                $image = $request->file('photo');
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/product');
                $image->move($destinationPath, $name);
            }
            DB::beginTransaction();
            $sale=0.00;
            if($request->sale_price)
                $sale=$request->sale_price;
                if ($request->file('photo')) {
                    $product = [
                        "name_ar" => $request->product_name_ar,
                        "name_en" => $request->product_name_en,
                        "product_code" => strtoupper($request->product_code),
                        "description_ar" => $request->description_ar,
                        "description_en" => $request->description_en,
                        "category_id" => $category_data->id,
                        "supplier_id" => $supplier_data->id,
                        "soldout" => $request->soldout,
                        "tax_code_id" => $taxcode_data->id,
                        "discount_code_id" => $discount_code_id,
                        "quantity" => $request->quantity,
                        "photo"=> "uploads/product/".$name,
                        "purchase_amount_excluding_tax" => $request->purchase_price,
                        "sale_amount_excluding_tax" => $sale,
                        "status" => $request->status,
                        "updated_by" => $request->logged_user_id
                    ];
                }else{
                    $product = [
                        "name_ar" => $request->product_name_ar,
                        "name_en" => $request->product_name_en,
                        "product_code" => strtoupper($request->product_code),
                        "description_ar" => $request->description_ar,
                        "description_en" => $request->description_en,
                        "category_id" => $category_data->id,
                        "supplier_id" => $supplier_data->id,
                        "soldout" => $request->soldout,
                        "tax_code_id" => $taxcode_data->id,
                        "discount_code_id" => $discount_code_id,
                        "quantity" => $request->quantity,
                        
                        "purchase_amount_excluding_tax" => $request->purchase_price,
                        "sale_amount_excluding_tax" => $sale,
                        "status" => $request->status,
                        "updated_by" => $request->logged_user_id
                    ];
                }
           

            $action_response = ProductModel::where('slack', $slack)
            ->update($product);
            if ($request->hasFile('photos')) {
                $files =$request->file('photos');
                foreach($files as $file) {
                    $name = time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/product');
                    $file->move($destinationPath, $name);
                    $photo = [
                        "product_id" => $product_id,
                        "photo" =>"uploads/product/".$name
                    ];
                    PhotosModel::create($photo);
                } 
            }
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product updated successfully", 
                    "data"    => $slack
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'product_name_en' => $this->get_validation_rules("name_label_en", true),
            'product_name_ar' => $this->get_validation_rules("name_label_ar", true),
            'product_code' => $this->get_validation_rules("codes", true),
            'purchase_price' => $this->get_validation_rules("numeric", true),
           // 'sale_price' => $this->get_validation_rules("numeric", true),
            'quantity' => $this->get_validation_rules("numeric", true),
            'supplier' => $this->get_validation_rules("slack", true),
            'category' => $this->get_validation_rules("slack", true),
           // 'tax_code' => $this->get_validation_rules("slack", true),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
            'photo'=>$this->get_validation_rules("photo", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
    public function validate_request_update($request)
    {
        $validator = Validator::make($request->all(), [
            'product_name_en' => $this->get_validation_rules("name_label_en", true),
            'product_name_ar' => $this->get_validation_rules("name_label_ar", true),
            'product_code' => $this->get_validation_rules("codes", true),
            'purchase_price' => $this->get_validation_rules("numeric", true),
           // 'sale_price' => $this->get_validation_rules("numeric", true),
            'quantity' => $this->get_validation_rules("numeric", true),
            'supplier' => $this->get_validation_rules("slack", true),
            'category' => $this->get_validation_rules("slack", true),
           // 'tax_code' => $this->get_validation_rules("slack", true),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    /**
     * get products from order page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_product(Request $request)
    {
        try {

            $product_code = $request->barcode;
            $product_title = $request->product_title;
            $product_category = $request->product_category;
      
            $query = ProductModel::select('products.slack as product_slack', 'products.product_code as product_code', 'products.name as product_name', 'products.sale_amount_excluding_tax', 'tax_codes.total_tax_percentage as tax_percentage', 'discount_codes.discount_percentage as discount_percentage','products.quantity as remaining_quantity')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->quantityCheck();

            if(isset($product_code) && $product_code != ''){
                $query->where([
                    ['products.product_code', 'like', '%'.trim($product_code).'%']
                ]);
            }
            if(isset($product_title) && $product_title != ''){
                $query->where([
                    ['products.name_en', 'like', '%'.trim($product_title).'%']
                ]);
            }
            if(isset($product_category) && $product_category != ''){
                $query->where([
                    ['category.slack', '=', trim($product_category)]
                ]);
            }

            $product_data = $query->get();

            if (empty($product_data)) {
                throw new Exception("Product not available", 400);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Product listed successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function generate_barcodes(Request $request){
        try {
            $product_code = $request->product_code;
            $no_of_barcodes = $request->no_of_barcodes;

            $validator = Validator::make($request->all(), [
                'product_code' => $this->get_validation_rules("text", true),
                'no_of_barcodes' => 'required|min:1|max:200',
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $upload_folder = Config::get('constants.upload.barcode.dir');
            $upload_path = Config::get('constants.upload.barcode.upload_path');
            $view_path = Config::get('constants.upload.barcode.view_path');
            $generator  = new \Picqer\Barcode\BarcodeGeneratorJPG();
            $barcode_type = $generator::TYPE_CODE_128;
            
            $barcode_array = [];
            $remove_file_array = [];
            $download_link = '';

            $product_code_array = explode(',', $product_code);
            $product_code_array = array_map('trim',$product_code_array);

            $product_array = ProductModel::select('slack','product_code')
            ->whereIn('product_code', $product_code_array)
            ->active()
            ->get();
            
            if ($product_array->isEmpty()) {
                throw new Exception("Invalid product codes provided", 400);
            }

            foreach($product_array as $product_array_item){

                $product_slack = $product_array_item->slack;
                $product_code = $product_array_item->product_code;
                    
                $barcode_data = $generator->getBarcode($product_code, $barcode_type);
                
                $filename = $product_slack.".jpg";

                Storage::disk('public')->put($upload_folder.$filename, $barcode_data);
                
                $barcode_path = $upload_path.$filename;
                $image_resize = Image::make($barcode_path); 
                $image_resize->resize(300, 100);
                $image_resize->save($barcode_path);
                    
                $barcode_array[] = [
                    'product_code' => $product_code,
                    'count' => $no_of_barcodes,
                    'product_barcode' => $barcode_path,
                ];

                $remove_file_array[] = $upload_folder.$filename;
            }
            
            if(count($barcode_array) >0){
                
                $date = Carbon::now();
                $current_date = $date->format('d-m-Y H:i');
                $store = $request->logged_user_store_code.'-'.$request->logged_user_store_name;

                $print_barcode_page = view('product.barcode.barcode_print', ['data' => $barcode_array, 'store' => $store, 'date' => $current_date])->render();

                $pdf_filename = "barcode_export_".date('Y_m_d_h_i_s')."_".uniqid().".pdf";
                
                ini_set("pcre.backtrack_limit", "5000000");
                set_time_limit(180);

                $mpdf_config = [
                    'mode'          => 'utf-8',
                    'format'        => 'A4',
                    'orientation'   => 'L',
                    'margin_left'   => 0,
                    'margin_right'  => 0,
                    'margin_top'    => 0,
                    'margin_bottom' => 0,
                    'margin_footer' => 1,
                    'tempDir' => storage_path()."/pdf_temp" 
                ];

                $css_file = 'css/barcode_print.css';
                $stylesheet = File::get(public_path($css_file));
                $mpdf = new Mpdf($mpdf_config);
                $mpdf->SetDisplayMode('real');
                $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->SetHTMLFooter('<div class="footer">store: '.$store.' | generated on: '.$current_date.' | page: {PAGENO}/{nb}</div>');
                $mpdf->WriteHTML($print_barcode_page);
                $mpdf->Output(public_path('storage/barcode').'/'.$pdf_filename, \Mpdf\Output\Destination::FILE);

                $download_link = asset($view_path.$pdf_filename);
            }

            Storage::disk('public')->delete($remove_file_array);

            return response()->json($this->generate_response(
                array(
                    "message" => "Barcodes generated successfully",
                    'link' => ($download_link != '')?$download_link:''
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }
    
    /**
     * get products for po page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function load_product_for_po(Request $request)
    {
        try {

            $keywords = $request->keywords;
            $supplier_slack = $request->supplier;

            $query = ProductModel::select('products.slack as product_slack', 'products.product_code as product_code', 'products.name as label', 'products.purchase_amount_excluding_tax', 'tax_codes.total_tax_percentage as tax_percentage', 'discount_codes.discount_percentage as discount_percentage')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->where('products.product_code', 'like', $keywords.'%')
            ->orWhere('products.name', 'like', $keywords.'%')
            ->where('suppliers.slack', $supplier_slack);
            
            $product_data = $query->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product listed successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }
}
