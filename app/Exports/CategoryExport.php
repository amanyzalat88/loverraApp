<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\CategoryResource;

use Carbon\Carbon;

class CategoryExport implements FromCollection, WithMapping, WithHeadings
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

        $query = Category::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config("app.date_format"), $from_created_date);
            $query = $query->where('category.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config("app.date_format"), $to_created_date);
            $query = $query->where('category.created_at', '>=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('category.status', $status);
        }

        $categories = $query->get();

        return $categories;
    }

    public function headings(): array
    {
        return [
            'CATEGORY CODE',
            'LABEL',
            'DESCRIPTION',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($category): array
    {
        $category = collect(new CategoryResource($category));
        return [
            $category['category_code'],
            $category['label'],
            $category['description'],            
            $category['status']['label'],
            $category['created_at_label'],
            $category['created_by']['fullname'],
            $category['updated_at_label'],
            $category['updated_by']['fullname']
        ];
    }
}
