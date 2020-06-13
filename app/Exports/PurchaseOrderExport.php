<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\PurchaseOrderResource;

use Carbon\Carbon;

class PurchaseOrderExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function collection()
    {
        $from_created_date = $this->data['from_created_date'];
        $to_created_date = $this->data['to_created_date'];
        $status = $this->data['status'];
        

        $query = PurchaseOrder::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config("app.date_format"), $from_created_date);
            $query = $query->where('purchase_orders.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config("app.date_format"), $to_created_date);
            $query = $query->where('purchase_orders.created_at', '>=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('purchase_orders.status', $status);
        }

        $purchase_orders = $query->get();

        return $purchase_orders;
    }

    public function headings(): array
    {
        return [
            'PO NUMBER',
            'PO REFERENCE',
            'ORDER DATE',
            'ORDER DUE DATE',
            'SUPPLIER CODE',
            'SUPPLIER NAME',
            'SUPPLIER ADDRESS',
            'CURRENCY NAME',
            'CURRENCY CODE',
            'SUBTOTAL EXCLUDING TAX',
            'TOTAL DISCOUNT AMOUNT',
            'TOTAL AFTER DISCOUNT',
            'TOTAL TAX AMOUNT',
            'SHIPPING CHARGE',
            'PACKING CHARGE',
            'TOTAL ORDER AMOUNT',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($order): array
    {
        $order = collect(new PurchaseOrderResource($order));
        return [
            $order['po_number'],
            $order['po_reference'],
            $order['order_date'],
            $order['order_due_date'],
            $order['supplier_code'],
            $order['supplier_name'],
            $order['supplier_address'],
            $order['currency_name'],
            $order['currency_code'],
            $order['subtotal_excluding_tax'],
            $order['total_discount_amount'],
            $order['total_after_discount'],
            $order['total_tax_amount'],
            $order['shipping_charge'],
            $order['packing_charge'],
            $order['total_order_amount'],
            $order['status']['label'],
            $order['created_at_label'],
            $order['created_by']['fullname'],
            $order['updated_at_label'],
            $order['updated_by']['fullname']
        ];
    }
}
