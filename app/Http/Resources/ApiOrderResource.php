<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Models\Mobile\Country;
use App\Models\Mobile\Customer;
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
                            $price= number_format($country->currency_rate_to_dinar * $this->total_order_amount,2);
                          
                        
                        }
            }
        }else{
            $country= Country::find($Hcountry);
            if($country)
                    {
                            $currency= $lang=='ar'?$country->currency_symbol:$country->currency_code;
                            $country1=$lang=='ar'?$country->name_ar:$country->name;
                            $price= number_format($country->currency_rate_to_dinar * $this->total_order_amount,2);
                          
                        
                        }

        }
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
                'total_order_amount' => $price,
                'currency'=>$currency,
                'country'=>$country1,
                'products' => $this->products($this->id) //ApiOrderProductResource::collection($this->products)   
            ];  
        //}
    }
}
