<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ContactResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->customer_id)
        {
            $cus=\App\Models\Customer::find($this->customer_id);
            return [
                'slack' => $this->slack,
                'name' => $cus->name,
                'phone' => $cus->phone,
                'email' => $cus->email,
                'message' => $this->message,
                'customer_id' => $this->customer_id,
                'photo' => $this->photo,
                 
                'detail_link' =>  route('contact_detail', ['slack' => $this->slack]),
                'created_at_label' => $this->parseDate($this->created_at),
                'updated_at_label' => $this->parseDate($this->updated_at),
                
            ];

        }
            else{
        return [
            'slack' => $this->slack,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'message' => $this->message,
            'customer_id' => $this->customer_id,
            'photo' => $this->photo,
             
            'detail_link' =>  route('contact_detail', ['slack' => $this->slack]),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            
        ];
    }
    }
}
