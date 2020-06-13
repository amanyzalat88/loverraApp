<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Models\Customer as CustomerModel;
use App\Models\Order as OrderModel;
use App\Models\User as UserModel;
use App\Models\Store as StoreModel;
use App\Models\Product as ProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\PurchaseOrder as PurchaseOrderModel;

class Dashboard extends Controller
{
    public function __construct(Request $request) { 
        $this->date = ($request->date != '')? $request->date : date("Y-m");
    }

    public function count_orders(Request $request){
        try {

            $count = OrderModel::closed()
            ->where('created_at', 'like', $this->date.'%')
            ->count();

            $count_formatted = number_format_short($count);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order count calculated successfully",
                    "data" => [
                        "count_raw" => $count,
                        "count_formatted" => $count_formatted
                    ],
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

    public function count_customers(Request $request){
        try {

            $count = CustomerModel::active()
            ->where('created_at', 'like', $this->date.'%')
            ->count();

            $count_formatted = number_format_short($count);

            return response()->json($this->generate_response(
                array(
                    "message" => "Customer count calculated successfully",
                    "data" => [
                        "count_raw" => $count,
                        "count_formatted" => $count_formatted
                    ],
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

    public function count_users(Request $request){
        try {
            $count = UserModel::active()
            ->where('created_at', 'like', $this->date.'%')
            ->count();

            $count_formatted = number_format_short($count);

            return response()->json($this->generate_response(
                array(
                    "message" => "User count calculated successfully",
                    "data" => [
                        "count_raw" => $count,
                        "count_formatted" => $count_formatted
                    ],
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

    public function count_products(Request $request){
        try {
            $count = ProductModel::active()
            ->where('created_at', 'like', $this->date.'%')
            ->count();

            $count_formatted = number_format_short($count);

            return response()->json($this->generate_response(
                array(
                    "message" => "Product count calculated successfully",
                    "data" => [
                        "count_raw" => $count,
                        "count_formatted" => $count_formatted
                    ],
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

    public function count_suppliers(Request $request){
        try {
            $count = SupplierModel::active()
            ->where('created_at', 'like', $this->date.'%')
            ->count();

            $count_formatted = number_format_short($count);

            return response()->json($this->generate_response(
                array(
                    "message" => "Supplier count calculated successfully",
                    "data" => [
                        "count_raw" => $count,
                        "count_formatted" => $count_formatted
                    ],
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

    public function count_stores(Request $request){
        try {
            $count = StoreModel::active()
            ->where('created_at', 'like', $this->date.'%')
            ->count();

            $count_formatted = number_format_short($count);

            return response()->json($this->generate_response(
                array(
                    "message" => "Store count calculated successfully",
                    "data" => [
                        "count_raw" => $count,
                        "count_formatted" => $count_formatted
                    ],
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

    public function count_order_value(Request $request){
        try {
            $sum = OrderModel::closed()
            ->where('created_at', 'like', $this->date.'%')
            ->sum('total_order_amount');

            $sum_formatted = number_format_short($sum);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order total calculated successfully",
                    "data" => [
                        "count_raw" => $sum,
                        "count_formatted" => $sum_formatted
                    ],
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

    public function count_total_revenue(Request $request){
        try {
            $sum = OrderModel::closed()
            ->where('created_at', 'like', $this->date.'%')
            ->sum('total_after_discount');

            $sum_formatted = number_format_short($sum);

            return response()->json($this->generate_response(
                array(
                    "message" => "Revenue calculated successfully",
                    "data" => [
                        "count_raw" => $sum,
                        "count_formatted" => $sum_formatted
                    ],
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

    public function get_monthly_order_count(Request $request){
        try {
            $order_count_data = OrderModel::selectRaw("COUNT(id) as order_count, DATE_FORMAT(created_at, '%e-%c') as order_date")
            ->closed()
            ->where('created_at', 'like', $this->date.'%')
            ->groupBy('order_date')
            ->get()
            ->toArray();

            $order_count_data_array = [];
            if (!empty($order_count_data)) {
                foreach($order_count_data as $order_count_data_item){
                    $order_count_data_array[$order_count_data_item['order_date']] = $order_count_data_item['order_count'];
                }
            }

            $year = date("Y", strtotime($this->date));
            $month = date("n", strtotime($this->date));
            $current_month = date("n");
            $current_day = date("j");

            $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $result = [];
            for($i = 1; $i <= $number_of_days; $i++){
                if($current_month == $month && $i > $current_day){
                    continue;                    
                }
                $loop_date = $i."-".$month;
                $order_count = 0;
                if(array_key_exists($loop_date, $order_count_data_array)){
                    $order_count = $order_count_data_array[$loop_date];
                }
                $result[] = [
                    "count" => $order_count,
                    "date" => $loop_date
                ];
            }

            $x_axis_data = array_column($result, 'date');
            $y_axis_data = array_column($result, 'count');
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Monthly order count matrix calculated successfully",
                    "data" => [
                        "x_axis" => $x_axis_data,
                        "y_axis" => $y_axis_data
                    ],
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

    public function get_monthly_order_revenue(Request $request){
        try {
            $order_count_data = OrderModel::selectRaw("SUM(total_after_discount) as order_revenue, DATE_FORMAT(created_at, '%e-%c') as order_date")
            ->closed()
            ->where('created_at', 'like', $this->date.'%')
            ->groupBy('order_date')
            ->get()
            ->toArray();

            $order_count_data_array = [];
            if (!empty($order_count_data)) {
                foreach($order_count_data as $order_count_data_item){
                    $order_count_data_array[$order_count_data_item['order_date']] = $order_count_data_item['order_revenue'];
                }
            }

            $year = date("Y", strtotime($this->date));
            $month = date("n", strtotime($this->date));
            $current_month = date("n");
            $current_day = date("j");

            $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $result = [];
            for($i = 1; $i <= $number_of_days; $i++){
                if($current_month == $month && $i > $current_day){
                    continue;                    
                }
                $loop_date = $i."-".$month;
                $order_count = 0;
                if(array_key_exists($loop_date, $order_count_data_array)){
                    $order_count = $order_count_data_array[$loop_date];
                }
                $result[] = [
                    "count" => $order_count,
                    "date" => $loop_date
                ];
            }

            $x_axis_data = array_column($result, 'date');
            $y_axis_data = array_column($result, 'count');
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Monthly order revenue matrix calculated successfully",
                    "data" => [
                        "x_axis" => $x_axis_data,
                        "y_axis" => $y_axis_data
                    ],
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

    public function count_total_purchases(Request $request){
        try {
            $sum = PurchaseOrderModel::closed()
            ->where('created_at', 'like', $this->date.'%')
            ->sum('total_order_amount');

            $sum_formatted = number_format_short($sum);

            return response()->json($this->generate_response(
                array(
                    "message" => "Purchases calculated successfully",
                    "data" => [
                        "count_raw" => $sum,
                        "count_formatted" => $sum_formatted
                    ],
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

    public function count_net_profit(Request $request){
        try {

            $revenue = $this->count_total_revenue($request);
            $revenue_data = $revenue->getData();
            $revenue_value = $revenue_data->data->count_raw;

            $purchases = $this->count_total_purchases($request);
            $purchase_data = $purchases->getData();
            $purchase_value = $purchase_data->data->count_raw;

            $net_profit = $revenue_value-$purchase_value;

            $net_profit_formatted = number_format_short($net_profit);

            return response()->json($this->generate_response(
                array(
                    "message" => "Net profit calculated successfully",
                    "data" => [
                        "count_raw" => $net_profit,
                        "count_formatted" => $net_profit_formatted
                    ],
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