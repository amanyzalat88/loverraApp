<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiOrderResource extends Resource
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
      /* if($request->id)
       {
            return [
                'id'=>$this->id,
                'order_number '=>$this->order_number,
                'payment_method' => $this->payment_method,
                'status' =>$this->Status($this->order_status_id,$lang),
                'total_order_amount' => $this->total_order_amount,
                'products' => ApiOrderProductResource::collection($this->products)    
            ];
        }else{*/
            return [
                'id'=>$this->id,
                'order_number '=>$this->order_number,
                'payment_method' => $this->payment_method,
                'status' =>$this->Status($this->order_status_id,$lang),
                'total_order_amount' => $this->total_order_amount,
                'products' => ApiOrderProductResource::collection($this->products)   
            ];  
        //}
    }
}
