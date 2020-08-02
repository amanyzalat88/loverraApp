<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiOrderProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function Nullable($value){
        return $value  == NULL ? "" : $value;
    }
    public function NullablePhoto($value){
        return $value ==  NULL ? url('public/images/4.jpg') : url('public/'.$value);
    }
    public function toArray($request)
    {
        $lang=  $request->header('lang');
        if(!$lang)
        $lang='ar';
        $name= $lang=='ar'? $this->name_ar: $this->name_en;
        $description=$lang=='ar'? $this->description_ar: $this->description_en;

        return [
            'id'=>$this->id,
            'product_code' => $this->product_code,
            'name' => $name,
            'quantity' => (int)$this->quantity,
            'price' => (double)$this->purchase_amount_excluding_tax,
            'sale' => (double)$this->sale_amount_excluding_tax,
            'photo'=>$this->NullablePhoto($this->photo),
            'purchase_amount_excluding_tax' => $this->purchase_amount_excluding_tax,
            'price' =>(double) $this->sale_amount_excluding_tax,
            'discount_code' => $this->discount_code,
            'discount_percentage' => $this->discount_percentage,
            'tax_code' => $this->tax_code,
            'tax_percentage' => $this->tax_percentage,
            'sub_total_purchase_price_excluding_tax' => (double)$this->sub_total_purchase_price_excluding_tax,
            'sub_total' =>(double) $this->sub_total_sale_price_excluding_tax,
            'discount_amount' => (double)$this->discount_amount,
            'total_after_discount' =>(double) $this->total_after_discount,
            'tax_amount' =>(double) $this->tax_amount,
            

        ];
    }
}