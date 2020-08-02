<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\SupplierResource;

use Carbon\Carbon;

class SupplierExport implements FromCollection, WithMapping, WithHeadings
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
        

        $query = Supplier::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config("app.date_format"), $from_created_date);
            $query = $query->where('suppliers.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config("app.date_format"), $to_created_date);
            $query = $query->where('suppliers.created_at', '>=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('suppliers.status', $status);
        }

        $orders = $query->get();

        return $orders;
    }

    public function headings(): array
    {
        return [
            'SUPPLIER CODE',
            'NAME',
            'EMAIL',
            'PHONE',
            'ADDRESS',
            'PINCODE',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($product): array
    {
        $supplier = collect(new SupplierResource($product));
        return [
            $supplier['supplier_code'],
            $supplier['name'],
            $supplier['email'],
            $supplier['phone'],
            $supplier['address'],
            $supplier['pincode'],
            $supplier['status']['label'],
            $supplier['created_at_label'],
            $supplier['created_by']['fullname'],
            $supplier['updated_at_label'],
            $supplier['updated_by']['fullname']
        ];
    }
}
