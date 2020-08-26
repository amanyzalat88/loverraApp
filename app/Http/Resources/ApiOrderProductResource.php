<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Country;
class ApiOrderProductResource extends Resource
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
        $Hcountry=  $request->header('country');
        if(!$Hcountry)
        $Hcountry=115;
        $token = $request->bearerToken();
		if($token)
        {
            if($customer=Customer::where('api_token',$token)->first())
            {
            
                $countryCust=$customer->country?$customer->country:'115';
            
                $country= Country::find($countryCust);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $this->purchase_amount_excluding_tax,2);
                            $sale = number_format($country->currency_rate_to_dinar * $this->sale_amount_excluding_tax,2) ;
                            $total= number_format($country->currency_rate_to_dinar *  $this->total_after_discount,2) ;
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
                            $total= number_format($country->currency_rate_to_dinar *  $this->total_after_discount,2) ;
                        }

        }
        return [
            'id'=>$this->id,
            'product_code' => $this->product_code,
            'name' => $name,
            'quantity' => (int)$this->quantity,
            'price' => (double)$price,
            'sale' => (double)$sale,
            'photo'=>$this->NullablePhoto($this->photo),
            'currency'=>$currency,
            'country'=>$country1,            
            'discount_code' => $this->discount_code,
            'discount_percentage' => $this->discount_percentage,
            'discount_amount' => (double)$this->discount_amount,
            'total' =>(double) $total,
           
        ];
    }
}