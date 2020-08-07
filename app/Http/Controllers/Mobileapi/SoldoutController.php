<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Soldout;
use Illuminate\Support\Str;
use DB;

class SoldoutController extends Controller
{
    public $successStatus = 200;

    public function store(Request $request)
    {
        $itemObj=null;
        $mess=null;
        $validator = Validator::make($request->all(), 
        ['email' => 'email','required'],
        ['product_id'=>'required']);
        if ($validator->fails()) {
            // $bb = array_values($validator->errors()->toArray());
             $bb = $validator->errors()->toArray();
             foreach ($bb as $k => $v) {
                 $mess[$k]=$v;
             }
             // $ErrorMess= json_encode($mess);
             return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
         }
        if( Soldout::where('product_id',$request->product_id)->where('email',$request->email)->count()>0)
       {
            $message = "Email Already exists";
            $this->status = '504';
            return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
        } else
         {
         $item =new Soldout();
         $item->product_id = $request->product_id;
        // $item->customer_id = $request->customer_id;
         $item->email = $request->email;
         
         if ($item->save()) {
              $itemObj = $item;
                 
             return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
         
     } else {
         $message = "Couldn't Not Create email";
         $this->status = '504';
        return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
     }
    }

    }
    
}
