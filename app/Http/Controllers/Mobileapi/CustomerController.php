<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Customer;
use Hash;
use DB;
use File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Mobile\Cart;
class CustomerController extends Controller
{
    public $successStatus = 200;

    protected function guard()
    {
        return Auth::guard('customer');
    }

    public function store(Request $request)
    {
            $itemObj=null;
		    $mess=null;
           /* $validator = Validator::make($request->all(), [
              'name' => ['string', 'max:255'],
              'phone' => 'numeric',
              'email' => 'email',
              'country' => 'string',
			 
             //'image' => 'image|Mimes:jpeg,jpg,png',
             ]);
      
        if ($validator->fails()) {
           // $bb = array_values($validator->errors()->toArray());
            $bb = $validator->errors()->toArray();
            foreach ($bb as $k => $v) {
                $mess[$k]=$v;
            }
            // $ErrorMess= json_encode($mess);
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
        }*/
        
        if($request->input('email'))
        {
           
            if(Customer::where('email',$request->input('email'))->where('id', '!=',$request->user()->id)->count()==0);
            else
            {
            $mess = "Email is token before";
            $this->status = '403';
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj],  $this->status);

              }
        }
        
        $item =Customer::find($request->user()->id);

        if ($item) {
            //$item->id = $request->user()->id;
            if($request->input('name'))
            $item->name = $request->input('name');
            if($request->input('phone'))
            $item->phone = $request->input('phone');
            if($request->input('country'))
            $item->country = $request->input('country');
        //If the password is not given in the request, do not change it then.
            if ($request->input('password')) {
                $item->password =Hash::make($request->password);
            }
            if($request->input('email'))
            $item->email = $request->input('email');
            }
            if ($item->save()) {
                $itemObj = $item;
                  unset($itemObj->password);
                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            
        } else {
            $mess = "Couldn't create user";
            $this->status = '403';

            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj],  $this->status);
        }
    }
     public function create(Request $request)
     {
            $itemObj=null;
		    $mess=null;
            $validator = Validator::make($request->all(), [
             'name' => ['required', 'string', 'max:255'],
             'phone' => 'numeric',
             'password' => 'required',
             'email' => 'email|unique:customers|required',
             'country' => 'required',
             ]);
        if ($validator->fails()) {
           
			 $bb = $validator->errors()->toArray();
            foreach ($bb as $k => $v) {
                $mess[$k]=$v;
            }
           //  $ErrorMess= json_encode($mess);
             
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
        }
        
            $item =new Customer();
            $item->name = $request->input('name');
            $item->phone = $request->input('phone');
            $item->password = Hash::make($request->password);
            $item->slack=$this->generate_slack("customers");
            $item->api_token= Str::random(60);
             $item->customer_type="Mobile-Api";
             $item->gender = $request->input('gender');
             $item->country = $request->input('country');
             // $item->uuid = $request->input('uuid');
            $item->email = $request->input('email');
            if ($item->save()) {
                if($request->guest_id)
                    {
                        Cart::where('customer_id',$request->guest_id)->update(array('customer_id'=>$item->id)); 
                    }
                 $itemObj = $item;
                   unset($itemObj->password);
                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            
        } else {
            $message = "Couldn't create user";
            $this->status = '504';
           return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
        }
    }
    public function login(Request $request)
    {
        $itemObj=null;
		$message=null;
        $validator = Validator::make($request->all(), [
             'email' => 'required',
             'password' => 'required',
             //'uuid' => 'required|string|min:16',
         ]);
        if ($validator->fails()) {
            $bb = $validator->errors()->toArray();
                foreach ($bb as $k => $v) {
                    $mess[$k]=$v;
                }
             //$ErrorMess= json_encode($mess);
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
        }
        // $password = Hash::make($request->password);
         
         $credentials = [
            'email' => $request['email'],
            'password' => $request->password,
        ];
        
        if ($this->guard()->attempt($credentials)) {
            $item = $this->guard()->user();
              unset($item->password);
            return response()->json(['status'=>true,'msg' => $message,'data'=>$item], $this->successStatus);
        } else {
            $message = "Email or password doesn't exist";
            $successStatus=503;
            return response()->json(['status'=>false,'msg' => $message,'data'=>null], $successStatus);
        }
    }
    public function show(Request $request)
    {
         $itemObj=null;
		 $message='';
        if ($itemObj = $request->user()) {
		// if ($itemObj = Customer::find($id)) {
            unset($itemObj->password);
            return response()->json(['status'=>true,'msg' => $message,'data'=>$itemObj], $this->successStatus);
        } else {
            $message = "User doesn't exist";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj], $this->successStatus);
        }
    }

      public function changePassword(Request $request)
		{
		  
		    $itemObj=null;
		    $mess=null;
            $validator = Validator::make($request->all(), [
             'newpassword' => ['required'],
            // 'phone' => 'required|numeric',
            // 'oldpassword' => 'required',
			// 'id'=>'required',
             //'image' => 'image|Mimes:jpeg,jpg,png',
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
        $itemObj =Customer::find($request->user()->id);
		 
        if ($itemObj) {
          
			
         
                $itemObj->password=Hash::make($request->newpassword);
				 $itemObj->save();
			unset($itemObj->password);
				 return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            }else{
			 $mess = "Old Password not the same  your password";
             $this->status = '403';
             return response()->json(['status'=>true,'msg' => $mess,'data'=>null],  $this->status);
			}
            
            
           
			
		}
	
	  public function ForgetPassword(Request $request)
		{
		  
		    $itemObj=null;
		    $mess='';
            $validator = Validator::make($request->all(), [
             'email' => ['required','email'],
             
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
        $itemObj =Customer::where('email',$request->email)->count();
        if ($itemObj>0) {
			$itemObj="send email";
			return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], 200);
           
           
        } else {
            $mess = "Email Not Found";
            $this->status = '403';
			 return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
            
        }
			
		}
}
