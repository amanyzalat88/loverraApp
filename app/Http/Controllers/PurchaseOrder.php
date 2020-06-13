<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;

use App\Http\Resources\PurchaseOrderResource;

use Mpdf\Mpdf;

use Illuminate\Support\Facades\File;

class PurchaseOrder extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('purchase_order.purchase_orders', $data);
    }

    //This is the function that loads the add/edit page
    public function add_purchase_order($slack = null){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        $data['action_key'] = ($slack == null)?'A_ADD_PURCHASE_ORDER':'A_EDIT_PURCHASE_ORDER';
        check_access(array($data['action_key']));

        $data['currency_list'] = CountryModel::select('currency_code', 'currency_name')
        ->where('currency_code', '!=', '')
        ->whereNotNull('currency_code')
        ->active()
        ->groupBy('currency_code')
        ->get();

        $data['purchase_order_data'] = null;
        if(isset($slack)){
            
            $purchase_order = PurchaseOrderModel::where('slack', '=', $slack)->first();
            if (empty($purchase_order)) {
                abort(404);
            }
            
            $purchase_order_data = new PurchaseOrderResource($purchase_order);
            $data['purchase_order_data'] = $purchase_order_data;
        }

        return view('purchase_order.add_purchase_order', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        $data['action_key'] = 'A_DETAIL_PURCHASE_ORDER';
        check_access([$data['action_key']]);

        $purchase_order = PurchaseOrderModel::where('slack', '=', $slack)->first();
        
        if (empty($purchase_order)) {
            abort(404);
        }

        $purchase_order_data = new PurchaseOrderResource($purchase_order);
        
        $data['purchase_order_data'] = $purchase_order_data;
        
        $po_statuses = [];
        
        if(check_access(['A_EDIT_STATUS_PURCHASE_ORDER'] ,true)){
            $po_statuses = MasterStatusModel::select('label','value_constant')->where([
                ['value_constant', '!=', strtoupper('CREATED')],
                ['key', '=', 'PURCHASE_ORDER_STATUS'],
                ['status', '=', '1']
            ])->active()->orderBy('value', 'asc')->get();
        }

        $data['po_statuses'] = $po_statuses;

        return view('purchase_order.purchase_order_detail', $data);
    }

    //This is the function that loads the print purchase order page
    public function print_purchase_order(Request $request, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        check_access([$data['sub_menu_key']]);

        $purchase_order = PurchaseOrderModel::where('slack', '=', $slack)->first();
        
        if (empty($purchase_order)) {
            abort(404);
        }

        $purchase_order_data = new PurchaseOrderResource($purchase_order);

        $print_logo_path = (config("app.company_logo") != "")?public_path(config('constants.upload.company.view_path').config("app.company_logo")):public_path(config('constants.upload.company.default'));
       
        $print_data = view('purchase_order.invoice.po_print', ['data' => json_encode($purchase_order_data), 'logo_path' => $print_logo_path])->render();

        $mpdf_config = [
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_left'   => 7,
            'margin_right'  => 7,
            'margin_top'    => 7,
            'margin_bottom' => 7,
            'tempDir' => storage_path()."/pdf_temp" 
        ];

        $stylesheet = File::get(public_path('css/purchase_order_print_invoice.css'));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLFooter('<div class="footer">Page: {PAGENO}/{nb}</div>');
        $mpdf->WriteHTML($print_data);
        $mpdf->Output('purchase_order_'.$purchase_order_data['po_number'].'.pdf', \Mpdf\Output\Destination::INLINE);

        //return view('purchase_order.invoice.po_print', ['data' => json_encode($purchase_order_data)]);
    }
    
}
