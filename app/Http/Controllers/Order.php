<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Order as OrderModel;
use App\Models\OrderProduct as OrderProductModel;
use App\Models\Customer as CustomerModel;
use App\Models\Store as StoreModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Category as CategoryModel;

use Mpdf\Mpdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Order extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_ORDERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('order.orders', $data);
    }

    //This is the function that loads the add/edit page
    public function add_order(Request $request, $slack = null){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_ORDERS';
        $data['action_key'] = ($slack == null)?'A_ADD_ORDER':'A_EDIT_ORDER';
        check_access(array($data['action_key']));

        $data['store_tax_percentage'] = null;
        $data['store_discount_percentage'] = null;

        $store_data = StoreModel::select('tax_code_id', 'discount_code_id', 'currency_code')
        ->where([
            ['id', '=', $request->logged_user_store_id],
            ['status', '=', 1]
        ])
        ->first();
        if (empty($store_data)) {
            return redirect('select_store');
        }

        $data['store_currency'] = $store_data->currency_code;

        if(isset($store_data->tax_code_id)){
            $taxcode_data = TaxcodeModel::select('total_tax_percentage')
            ->where('id', '=', $store_data->tax_code_id)
            ->active()
            ->first();
            $data['store_tax_percentage'] = (isset($taxcode_data->total_tax_percentage))?$taxcode_data->total_tax_percentage:0.00;
        }

        if(isset($store_data->discount_code_id)){
            $discountcode_data = DiscountcodeModel::select('discount_percentage')
            ->where('id', '=', trim($store_data->discount_code_id))
            ->active()
            ->first();
            $data['store_discount_percentage'] = (isset($discountcode_data->discount_percentage))?$discountcode_data->discount_percentage:0.00;
        }

        $categories = CategoryModel::select('slack', 'category_code', 'label_en')->sortLabelAsc()->get();
        $data['categories'] = (!empty($categories))?$categories:[];

        $payment_methods = PaymentMethodModel::select('slack', 'label_en')
        ->active()
        ->get();
        $data['payment_methods'] = (!empty($payment_methods))?$payment_methods:[];

        $data['order_data'] = null;
        if(isset($slack)){
            $order = OrderModel::where('slack', $slack)
            ->first();

            $data['order_data']['slack'] = $order->slack;

            $data['order_data']['order'] = [
                'order_number' => $order->order_number,
                'store_level_total_tax_percentage' => $order->store_level_total_tax_percentage,
                'store_level_total_discount_percentage' => $order->store_level_total_discount_percentage,
                'sub_total' => $order->sale_amount_subtotal_excluding_tax,
                'tax_total' => $order->total_tax_amount,
                'total' => $order->total_order_amount,
                'customer_number' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'payment_method' => $order->payment_method_slack
            ];

            $order_products = OrderProductModel::where('order_id', $order->id)
            ->get();
            $cart = [];

            if(count($order_products)>0){
                foreach($order_products as $order_product){
                    $cart[$order_product->product_slack] = [
                        "product_slack"     => $order_product->product_slack,
                        "product_code"      => $order_product->product_code,
                        "name"              => $order_product->name,
                        "price"             => $order_product->sale_amount_excluding_tax,
                        "quantity"          => $order_product->quantity,
                        "tax_percentage"    => $order_product->tax_percentage,
                        "discount_percentage" => $order_product->discount_percentage,
                        "total_price"       => $order_product->total_amount
                    ];
                }
            }
            
            $data['order_data']['cart'] = json_encode($cart);
        }

        return view('order.add_order', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_ORDERS';
        $data['action_key'] = 'A_DETAIL_ORDER';
        check_access([$data['action_key']]);

        $order_data = $this->get_order_data($slack);

        $data['order_data'] = $order_data;

        $data['delete_order_access'] = check_access(['A_DELETE_ORDER'] ,true);

        return view('order.order_detail', $data);
    }

    //This is the function that loads the print order page
    public function print_order(Request $request, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_ORDERS';
        check_access([$data['sub_menu_key']]);

        $order_data = $this->get_order_data($slack);
        if($order_data['status']['value'] != 1){
            abort(404);
        }

        $invoice_print_type = $order_data['store']['invoice_type'];
        
        switch($invoice_print_type){
            case 'A4':
                $view_file = 'order.invoice.a4_print';
                $css_file = 'css/order_a4_print_invoice.css';
                $format = 'A4';
                $print_logo_path = (config("app.company_logo") != "")?public_path(config('constants.upload.company.view_path').config("app.company_logo")):public_path(config('constants.upload.company.default'));
            break;
            case 'SMALL':
                $view_file = 'order.invoice.thermal_print';
                $css_file = 'css/order_thermal_print_invoice.css';
                $format = [78, 297];
                $print_logo_path = '';
            break;
            default:
                $view_file = 'order.invoice.thermal_print';
                $css_file = 'css/order_thermal_print_invoice.css';
                $format = [78, 297];
                $print_logo_path = '';
            break;
        }

        $print_data = view($view_file, ['data' => json_encode($order_data), 'logo_path' => $print_logo_path])->render();

        $mpdf_config = [
            'mode'          => 'utf-8',
            'format'        => $format,
            'orientation'   => 'P',
            'margin_left'   => 7,
            'margin_right'  => 7,
            'margin_top'    => 7,
            'margin_bottom' => 7,
            'tempDir' => storage_path()."/pdf_temp" 
        ];

        $stylesheet = File::get(public_path($css_file));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($print_data);
        $mpdf->Output('order_'.$order_data['order_number'].'.pdf', \Mpdf\Output\Destination::INLINE);

        //return view($view_file, $data);
    }

    public function get_order_data($slack){
        $data['order_data'] = null;

        if(isset($slack)){

            $order = OrderModel::select('orders.*')->where('orders.slack', $slack)
            ->first();

            if (empty($order)) {
                abort(404);
            }

            $order_data = new OrderResource($order);
           
            $order_products_array = collect($order_data->products)->toArray();
          
            $total_qty_array = data_get($order_products_array, '*.quantity', 0);
            $total_quantity = array_sum($total_qty_array);
            
            $order_data = collect($order_data);
            $order_data->put('total_quantity', $total_quantity);
            $data = $order_data->all();
           
        }
        return $data;
    }
}
