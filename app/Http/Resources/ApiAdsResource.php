<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiAdsResource extends Resource
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

    public function toArray($request)
    {
        $lang=  $request->header('lang');
        $title= $lang=='ar'? $this->title_ar: $this->title_en;
        $description= $lang=='ar'? $this->description_ar: $this->description_en;
		
        return [
            'id'=>$this->id,
            'title'=>$title,
            'description'=>$description,
            'photo'=>$this->NullablePhoto($this->photo),
            'path'=>$this->category_id  
        ];
    }
}
