<?php

namespace App\Http\Controllers\Mobileapi;
use App\Http\Controllers\Controller;
use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Mobile\PaymentMethod as PaymentMethodModel;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\ApiPaymentMethodResource;

class PaymentMethodController extends Controller
{
    public $successStatus = 200;
 
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data=null;
        $message='';
          
        $Payment= PaymentMethodModel::all();  
        
       if ($Payment->count()>0) {
            $result=ApiPaymentMethodResource::collection($Payment);
                
           return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
       } else {
           $message = "Not Products yet ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }

   
}
