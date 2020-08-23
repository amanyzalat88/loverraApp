<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Country;
class ApiBoxesColorResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
    public function NullablePhoto($value){
        return $value ==  NULL ? url('public/images/4.jpg') :   url('public/'.$value) ;
    }
    public function Nullable($value){
        return $value === NULL ? "" : $value;
    }
    public function toArray($request)
    {
        $lang=  $request->header('lang');
        if(!$lang)
        $lang='ar';
        $Hcountry=  $request->header('country');
        if(!$lang)
        $lang='ar';
        if(!$Hcountry)
        $Hcountry=115;
        $name= $lang=='ar'? $this->name_ar: $this->name_en;
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
                            $price= number_format($country->currency_rate_to_dinar * $this->price,2);
                          
                        
                        }
            }
        }else{
            $country= Country::find($Hcountry);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $this->price,2);
                          
                        
                        }

        }
        return [
            'id'=>$this->id,
            'name'=>$this->Nullable($name),
            'price'=>(double)$price,
            'currency'=>$currency,
            'country'=>$country1,
            'photo'=>$this->NullablePhoto($this->photo),   
        ];
    }
}
