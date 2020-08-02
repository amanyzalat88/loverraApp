<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiBoxesCardResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
    public function NullablePhoto($value){
        return $value ==  NULL ? url('public/images/4.jpg') :  url('public/'.$value) ;
    }
    public function Nullable($value){
        return $value === NULL ? "" : $value;
    }
    public function toArray($request)
    {
        $lang=  $request->header('lang');
        if(!$lang)
        $lang='ar';
        $name= $lang=='ar'? $this->name_ar: $this->name_en;
		
        return [
            'id'=>$this->id,
            'name'=>$this->Nullable($name),
            'price'=>(double)$this->price,
            'photo'=>$this->NullablePhoto($this->photo) ,
            
                    
        ];
    }
}
