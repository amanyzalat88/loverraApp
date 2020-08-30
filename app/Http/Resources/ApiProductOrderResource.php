<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Customer;
use App\Models\Mobile\Favorite;
use App\Models\Mobile\Country;
class ApiProductOrderResource extends Resource
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
        $Hcountry=  $request->header('country');
        if(!$lang)
        $lang='ar';
        if(!$Hcountry)
        $Hcountry=115;
        $name= $lang=='ar'? $this->name_ar: $this->name_en;

        $description=$lang=='ar'? $this->description_ar: $this->description_en;
        $is_favorite="0";
        
    
        $token = $request->bearerToken();
		if($token)
        {
            if($customer=Customer::where('api_token',$token)->first())
            {
           if(Favorite::where('customer_id', $customer->id)->where('product_id',$this->id)->count()>0)
           {
              $is_favorite="1";
           }
           $countryCust=$customer->country?$customer->country:'115';
            
           $country= Country::find($countryCust);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $this->purchase_amount_excluding_tax,2);
                            $sale = number_format($country->currency_rate_to_dinar * $this->sale_amount_excluding_tax,2) ;
                        
                        }
            }
        }else{
            $country= Country::find($Hcountry);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $this->purchase_amount_excluding_tax,2);
                            $sale = number_format($country->currency_rate_to_dinar * $this->sale_amount_excluding_tax,2) ;
                        
                        }

        }
        
        
        return [
			'id'=>$this->id,
            'product_code' => $this->product_code,
            'name' => $name,
           // 'description' => $this->Nullable($description),
            'quantity' => (int)$this->quantity,
            'currency'=>$currency,
            'country'=>$country1,
            'price' => (double)$price,
            'sale' => (double)$sale,
            'photo'=>$this->NullablePhoto($this->photo),
			'soldout'=>$this->soldout,
            'available'=>$this->available,
            'is_favorite'=>$is_favorite,
			
        ];
    }
}
