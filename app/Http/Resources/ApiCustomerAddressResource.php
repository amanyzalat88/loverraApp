<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiCustomerAddressResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
            'id'=>$this->id,
            'is_default'=>$this->is_default,
            'slack' => $this->slack,
            'delivery' => $this->delivery_area,
            'building'=> $this->building,
            'street' => $this->street,
            'flatnumber' => $this->flatnumber,
            'landmark' => $this->landmark,
            'customer_id'=>$this->customer_id   
        ];
    }
}
