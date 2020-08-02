<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Contactus;
use App\Models\Mobile\Customer;
use Hash;
use DB;
use File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ContactusController extends Controller
{
    public $successStatus = 200;

   

    public function store(Request $request)
    {
            $itemObj=null;
            $mess=null;
            if($request->name){
                    $validator = Validator::make($request->all(), [
                    'name' => ['required','string', 'max:255'],
                    'phone' => 'required','numeric',
                    'email' => 'required','email',
                    'message' => 'required'
                    ]);
            
                if ($validator->fails()) {
                   // $bb = array_values($validator->errors()->toArray());
                    $bb = $validator->errors()->toArray();
                    foreach ($bb as $k => $v) {
                        $mess[$k]=$v;
                    }
                    // $ErrorMess= json_encode($mess);
                    return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
                }
                $item =new Contactus();
                $item->name = $request->input('name');
                $item->phone = $request->input('phone');
                $item->email = $request->input('email');
                $item->message=$request->input('message');
                if ($request->hasFile('photo')) {
                    $image = $request->file('photo');
                    $name = time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/contact');
                    $image->move($destinationPath, $name);
                    $item->photo= "uploads/contact/".$name;
                }
               
                if ($item->save()) {
                    $lang=  $request->header('lang');
                    if(!$lang)
                      $lang='ar';
                      $mess= $lang=='ar'?'تم ارسال الرسالة بنجاح سوف يتم الرد عليكم فى اقرب وقت':'Message Send Succefully , We contact with you as soon as possible';
                    return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
                }
           }else{
                $validator = Validator::make($request->all(), [ 
                    'message' => 'required'
                    ]);
            
                if ($validator->fails()) {
                // $bb = array_values($validator->errors()->toArray());
                    $bb = $validator->errors()->toArray();
                    foreach ($bb as $k => $v) {
                        $mess[$k]=$v;
                    }
                    // $ErrorMess= json_encode($mess);
                    return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
                }
                $token = $request->bearerToken();
                $customer_id=Customer::where('api_token',$token)->first()->id;
                $item =new Contactus();
                $item->customer_id = $customer_id;
                if ($request->hasFile('photo')) {
                    $image = $request->file('photo');
                    $name = time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/uploads/contact');
                    $image->move($destinationPath, $name);
                    $item->photo= "uploads/contact/".$name;
                }
                $item->message=$request->input('message');
                if ($item->save()) {
                    $lang=  $request->header('lang');
                    if(!$lang)
                    $lang='ar';
                    $mess= $lang=='ar'?'تم ارسال الرسالة بنجاح سوف يتم الرد عليكم فى اقرب وقت':'Message Send Succefully , We contact with you as soon as possible';
                    return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
                }

      }
      
    }
     
    
}
