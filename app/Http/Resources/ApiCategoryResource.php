<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiCategoryResource extends Resource
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
        $label=$lang=='ar'? $this->label_ar: $this->label_en;
        $description= $lang=='ar'? $this->description_ar: $this->description_en;
		
        return [
			'id'=>$this->id,
            'parent'=>$this->parent,
            'category_code' => $this->category_code,
            'label' => $label,
            'description' =>$this->Nullable($description),
            'has_sub'=>$this->hasSub($this->id),
            'photo'=>$this->NullablePhoto($this->photo)
        ];
    }
}
