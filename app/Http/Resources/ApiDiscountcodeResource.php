<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiDiscountcodeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    function type($t)
    {
        switch($t)
        {
        case '1':
         return 'منتج';
         case '2':
         return 'قسم';
         case '3':
         return 'اجمالى الفاتورة';
        }
    }
    public function toArray($request)
    {
        return [
            'slack' => $this->slack,
            'label' => $this->label,
            'discount_code' => $this->discount_code,
            'discount_percentage' => $this->discount_percentage,
            'description' => $this->description,
            'discount_type' => $this->type($this->discount_type),
            'discount_num' => $this->discount_num,
            'discount_from' => $this->discount_from,
            'discount_to' => $this->discount_to,
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_DISCOUNTCODE'], true))?route('discount_code', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
