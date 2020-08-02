<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Ads;
use Illuminate\Support\Str;
use App\Http\Resources\ApiAdsResource;

class AdsController extends Controller
{
    public $successStatus = 200;

    
    public function index(Request $request)
    {
        
         $data=null;
         $message='';
         
           $item =Ads::where('id',$request->id)->get();
        
       if ($item->count()>0) {
            $result=ApiAdsResource::collection($item);
                    
           return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
       } else {
           $message = "Not  Ads Photos yet ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }
   
}
