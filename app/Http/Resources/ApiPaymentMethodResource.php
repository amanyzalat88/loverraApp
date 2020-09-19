<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiPaymentMethodResource extends Resource
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
        if(!$lang)
            $lang='ar';
            $name= $lang=='ar'? $this->label_ar: $this->label_en;
        return [
            'id'=>$this->id,
            'slack' => $this->slack,
            'label' => $name,
            'icon'=>$this->icon
            
        ];
    }
}
