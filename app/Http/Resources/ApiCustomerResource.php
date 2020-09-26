<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiCustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
     

    public function toArray($request)
    {
        $lang=  $request->header('lang');
       
		
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'api_token'=>$this->api_token,
            'phone'=>$this->phone ,
            'gender'=>(int)$this->gender ,
            'country'=>(int)$this->country 
        ];
    }
}
