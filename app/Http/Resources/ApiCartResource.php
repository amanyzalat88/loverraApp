<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiCartResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function NullablePhoto($value){
        return $value ==  NULL ? url('public/images/4.jpg') : url('public/'.$value);
    }
    public function toArray($request)
    {
        $lang=  $request->header('lang');
            if(!$lang)
            $lang='ar';
        $name= $lang=='ar'? 'name_ar': 'name_en';

        $product=$this->product($this->product_id);
       
        return [
            'id'=>$this->product_id,
            'name'=>$product->$name,
            'price'=>(double)$product->purchase_amount_excluding_tax,
            'photo'=>$this->NullablePhoto($product->photo),
            'sale'=>(double)$product->sale_amount_excluding_tax,
            'quantity'=>$this->quantity
        ];
    }
}
