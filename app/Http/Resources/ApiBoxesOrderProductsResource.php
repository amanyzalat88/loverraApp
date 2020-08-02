<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiBoxesOrderProductsResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
    public function NullablePhoto($value){
        return $value ==  NULL ? url('public/images/4.jpg') :  $value ;
    }
    public function Nullable($value){
        return $value === NULL ? "" : $value;
    }
    public function toArray($request)
    {
        
		
        return [
			'id'=>$this->id,
            'product_id'=>$this->product_id,
            'order_id'=>$this->order_id,
           
        ];
    }
}
