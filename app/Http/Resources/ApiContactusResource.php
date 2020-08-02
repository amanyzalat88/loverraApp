<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiContactusResource extends Resource
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
        $lang=  $request->header('lang');
        $photo= $lang=='ar'? $this->photo_ar: $this->photo_en;
       
		
        return [
			'id'=>$this->id,
            'name'=>$this->Nullable($this->name) ,
            'phone'=>$this->Nullable($this->phone),
            'email'=>$this->Nullable($this->email),
            'message'=>$this->Nullable($this->message),
            'customer_id'=>$this->customer_id        
        ];
    }
}
