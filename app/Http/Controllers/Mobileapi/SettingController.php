<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Mobile\setting;
use App\Http\Resources\ApiSettingResource;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public $successStatus = 200;
 
    public function index(Request $request)
    {
         $data=null;
		 $message='';
		 
		  $cats= setting::get();  
		 
        if ($cats->count()>0) {
           
             $result=ApiSettingResource::collection($cats);
            
             
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
        } else {
            $message = "Not Setting yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
   
	
  
}
