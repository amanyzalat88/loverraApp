<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

use App\Models\Mobile\Slider;
use Illuminate\Support\Str;
use App\Http\Resources\ApiSliderResource;

class SliderController extends Controller
{
    public $successStatus = 200;

    
    public function index(Request $request)
    {
        
         $data=null;
         $message='';
         
           $item =Slider::all();
        
       if ($item->count()>0) {
            $result=ApiSliderResource::collection($item);
            
                    
           return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
       } else {
           $message = "Not Slider Photos yet ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }
   
}
