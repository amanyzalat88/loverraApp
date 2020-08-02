<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;

use App\Imports\DataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;

use App\Exports\UserExport;
use App\Exports\ProductExport;
use App\Exports\OrderExport;
use App\Exports\CustomerExport;
use App\Exports\CategoryExport;
use App\Exports\DiscountcodeExport;
use App\Exports\StoreExport;
use App\Exports\SupplierExport;
use App\Exports\TaxcodeExport;
use App\Exports\PurchaseOrderExport;

class Report extends Controller
{

    public function __construct() {
        $this->view_path = Config::get('constants.upload.reports.view_path');
    }

    public function user_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'role' => $request->role,
                'status' => $request->status,
            ];

            $filename = 'user_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new UserExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "User report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function product_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'supplier' => $request->supplier,
                'category' => $request->category,
                'tax_code' => $request->tax_code,
                'discount_code' => $request->discount_code,
                'status' => $request->status,
            ];

            $filename = 'product_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new ProductExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Product report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function order_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'order_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new OrderExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function purchase_order_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'purchase_order_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new PurchaseOrderExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Purchase Order report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function customer_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'customer_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new CustomerExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Customer report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function store_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'store_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new StoreExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Store report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function taxcode_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'taxcode_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new TaxcodeExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Taxcode report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function discountcode_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'discountcode_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new DiscountcodeExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Discount code report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function supplier_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'supplier_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new SupplierExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Supplier code report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function category_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'category_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new CategoryExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Category code report generated successfully",
                    "data" => '',
                    "link" => $download_link
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