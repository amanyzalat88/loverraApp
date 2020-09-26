<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiCountryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
    public function NullablePhoto($value){
        return $value == NULL ? url('public/images/4.jpg') : $value;
    }

    public function toArray($request)
    {
        $lang=  $request->header('lang');
        $name= $lang=='ar'? $this->name_ar: $this->name;
       
        return [
            'id'=>$this->id,
            'name'=>$name,
            'icon'=>$this->NullablePhoto($this->icon),
        ];
    }
}
