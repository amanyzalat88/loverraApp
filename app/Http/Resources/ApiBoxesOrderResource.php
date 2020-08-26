<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Country;
class ApiBoxesOrderResource extends Resource
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
    public function Nullable($value){
        return $value === NULL ? "" : $value;
    }
    public function toArray($request)
    {
        $lang=  $request->header('lang');
        if(!$lang)
        $lang='ar';
        $name= $lang=='ar'? 'name_ar': 'name_en';
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
                            $total=$this->total($this->id,$Hcountry);
                            $price= number_format($country->currency_rate_to_dinar * $total,2);
                          
                        
                        }
            }
        }else{
            $country= Country::find($Hcountry);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $total=$this->total($this->id,$Hcountry);
                            $price= number_format($country->currency_rate_to_dinar * $total,2);
                          
                        
                        }

        }
        
      
        return [
			'id'=>$this->id,
            'name'=>$this->BoxesName($this->box_id,$name),
            'color'=>$this->Color($this->color_id,$name),
            'card'  =>$this->Card($this->card_id,$name),
            'price'=>(double)$price,
            'currency'=>$currency,
            'country'=>$country1,
            'photo'=>url(''.$this->BoxesPhoto($this->box_id).'') ,
           'products'=>$this->products($this->id)   
        ];
    }
}
