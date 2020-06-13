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
            $validator = Validator::make($request->all(), [
             'name' => ['required', 'string', 'max:255'],
             'phone' => 'required|numeric',
             'email' => 'email',
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
        $item =Customer::find($request->user()->id);
        if ($item) {
            //$item->id = $request->user()->id;
            $item->name = $request->input('name');
            $item->phone = $request->input('phone');
        //If the password is not given in the request, do not change it then.
            if ($request->input('password')) {
                $item->password =Hash::make($request->password);
            }
            $item->email = $request->input('email');
            }
            if ($item->save()) {
                $itemObj = $item;
                  unset($itemObj->password);
                return response()->json(['status'=>true,'data'=>$itemObj,'msg'=>'User Updated successfully'], $this->successStatus);
            
        } else {
            $message = "Couldn't create user";
            $this->status = '504';

            return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
        }
    }
     public function create(Request $request)
     {
            $itemObj=null;
            $validator = Validator::make($request->all(), [
             'name' => ['required', 'string', 'max:255'],
             'phone' => 'required|numeric|unique:customers',
             'password' => 'required',
             'email' => 'email',
             //'image' => 'image|Mimes:jpeg,jpg,png',
             ]);
        if ($validator->fails()) {
            $bb = $validator->errors()->toArray();
            foreach ($bb as $k => $v) {
                $mess[$k]=$v;
            }
             //$ErrorMess= json_encode($mess);
             
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
        }
            $item =new Customer();
            $item->name = $request->input('name');
            $item->phone = $request->input('phone');
            $item->password = Hash::make($request->password);
            $item->slack=$this->generate_slack("customers");
            $item->api_token= Str::random(60);
             $item->customer_type="Mobile-Api";
             // $item->uuid = $request->input('uuid');
           $item->email = $request->input('email');
            if ($item->save()) {
                 $itemObj = $item;
                   unset($itemObj->password);
                return response()->json(['status'=>true,'data'=>$itemObj,'msg'=>'Register successfully'], $this->successStatus);
            
        } else {
            $message = "Couldn't create user";
            $this->status = '504';
           return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
        }
    }
    public function login(Request $request)
    {
        $itemObj=null;
        $validator = Validator::make($request->all(), [
             'phone' => 'required',
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
            'phone' => $request['phone'],
            'password' => $request->password,
        ];
        
        if ($this->guard()->attempt($credentials)) {
            $item = $this->guard()->user();
              unset($item->password);
            return response()->json(['status'=>true,'data'=>$item,'msg'=>'Authenticated successfully'], $this->successStatus);
        } else {
            $message = "Mobile or password doesn't exist";
            $successStatus=503;
            return response()->json(['status'=>false,'msg' => $message,'data'=>null], $successStatus);
        }
    }
    public function show(Request $request)
    {
         $itemObj=null;
        if ($itemObj = $request->user()) {
            unset($itemObj->password);
            return response()->json(['status'=>true,'data'=>$itemObj,'msg'=>'Profile successfully'], $this->successStatus);
        } else {
            $message = "User doesn't exist";

            return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj], $this->successStatus);
        }
    }

   
}
