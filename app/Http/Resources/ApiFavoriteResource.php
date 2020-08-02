<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiFavoriteResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function Nullable($value){
        return $value === NULL ? "" : $value;
    }
    public function NullablePhoto($value){
        return $value == NULL ? url('public/images/4.jpg') : url('public/'.$value);
    }
    public function toArray($request)
    {
        
        $lang=  $request->header('lang');
        $name=$lang=='ar'? $this->name_ar: $this->name_en;
		
        return  
			 new ApiProductResource($this->product)
         ;
    }
}
