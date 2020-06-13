<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CustomerAddressResource extends Resource
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
            'slack' => $this->slack,
            'delivery' => $this->delivery_area,
            'building'=> $this->building,
            'street' => $this->street,
            'flatnumber' => $this->flatnumber,
            'landmark' => $this->landmark,
            'country' => $this->country,
            'city' =>  $this->city,
            'customer_id'=>$this->customer_id,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at)
            
        ];
    }
}
