<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Customer;
use App\Models\Mobile\Favorite;
class ApiProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
	public function Nullable($value){
        return $value  == NULL ? "" : $value;
    }
    public function NullablePhoto($value){
        return $value ==  NULL ? url('public/images/4.jpg') : url('public/'.$value);
    }
    public function toArray($request)
    {
        
        $lang=  $request->header('lang');
        if(!$lang)
        $lang='ar';
        $name= $lang=='ar'? $this->name_ar: $this->name_en;

        $description=$lang=='ar'? $this->description_ar: $this->description_en;
        $is_favorite="0";
        $token = $request->bearerToken();
		if($token)
        {
            $customer_id=Customer::where('api_token',$token)->first()->id;
           if(Favorite::where('customer_id', $customer_id)->where('product_id',$this->id)->count()>0)
              $is_favorite="1";
        }
        
        return [
			'id'=>$this->id,
            'product_code' => $this->product_code,
            'name' => $name,
            'description' => $this->Nullable($description),
            'quantity' => (int)$this->quantity,
            'price' => (double)$this->purchase_amount_excluding_tax,
            'sale' => (double)$this->sale_amount_excluding_tax,
            'category' =>$this->Category($this->category_id,$lang),
            'supplier' => $this->supplier($this->supplier_id),
            'photo'=>$this->NullablePhoto($this->photo),
			'soldout'=>$this->soldout,
            'available'=>$this->available,
            'is_favorite'=>$is_favorite,
			'photos'=>$this->photos($this->id)
        ];
    }
}
