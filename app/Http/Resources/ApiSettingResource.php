<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiSettingResource extends Resource
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
        $address= $lang=='ar'? $this->address_ar: $this->address_en;
        $about=$lang=='ar'? $this->about_ar: $this->about_en;
		
        return [ 
			
            'address'=>$this->Nullable($address),
            'phone'=>$this->Nullable($this->phone),
            'email'=>$this->Nullable($this->email),
            'about'=>$this->Nullable($about),
            'twitter'=>$this->Nullable($this->twitter),
            'insta'=>$this->Nullable($this->insta)       
        ];
    }
}
