<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Country;
class ApiCartResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
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
        $name= $lang=='ar'? 'name_ar': 'name_en';

        $product=$this->product($this->product_id);
        
        $token = $request->bearerToken();
		if($token)
        {
            if($customer=Customer::where('api_token',$token)->first())
            {
            
           $country= Country::find($customer->country);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $product->purchase_amount_excluding_tax,2);
                            $sale = number_format($country->currency_rate_to_dinar * $product->sale_amount_excluding_tax,2) ;
                        
                        }
            }
        }else{
            $country= Country::find($Hcountry);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $product->purchase_amount_excluding_tax,2);
                            $sale = number_format($country->currency_rate_to_dinar * $product->sale_amount_excluding_tax,2) ;
                        
                        }

        }
        return [
            'id'=>$this->product_id,
            'name'=>$product->$name,
            'price'=>(double)$price,
            'sale'=>(double)$sale,
            'country'=>$country1,
            'currency'=>$currency,
            'photo'=>$this->NullablePhoto($product->photo),
            'quantity'=>$this->quantity
        ];
    }
}
