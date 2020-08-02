<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobile\CustomerAddress;
use App\Http\Resources\ApiCustomerAddressResource;
use Illuminate\Support\Str;
use Validator;
class CustomerAddressController extends Controller
{
    public $successStatus = 200;
 
    public function index(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		  $cats= CustomerAddress::where('customer_id',$request->user()->id)->paginate($count);  
		 
        if ($cats->count()>0) {
           
             $result=ApiCustomerAddressResource::collection($cats);
            
             $data= [
                    'total' => $cats->total(),
                    'count' => $cats->count(),
                    'per_page' => intval($cats->perPage()),
                    'current_page' => $cats->currentPage(),
                    'total_pages' => $cats->lastPage(),
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Address yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function delete(Request $request)
    {
            $itemObj=null;
            $mess=null;
           
        $item =CustomerAddress::find($request->id);
        if ($item) {
           
            if ($item->delete()) {
              $mess="deleted address";
                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            }
        else {
            $mess = "can't deleted address";
            $this->status = '403';
    
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj],  $this->status);
        }
    
        }
        else{
            $mess = "can't   address not found";
            $this->status = '403';
    
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj],  $this->status);
        }
    }
	
    public function store(Request $request)
    {
           $itemObj=null;
           $mess=null;
           $validator = Validator::make($request->all(), [
            'delivery_area' => ['required', 'string', 'max:255'],
            'street' => 'required',
            
            //'image' => 'image|Mimes:jpeg,jpg,png',
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
       }
           $item =new CustomerAddress();
           $item->slack=$this->generate_slack("customers_addresses");
           $item->delivery_area = $request->input('delivery_area');
           $item->building = $request->input('building');
           $item->street =  $request->street;
           $item->flatnumber=$request->flatnumber;
           $item->landmark= $request->landmark;
           $item->customer_id=$request->user()->id;
           
           if(CustomerAddress::where('customer_id',$request->user()->id)->count()==0)
           {
            $item->is_default=1;
           }
           if ($item->save()) {
                $itemObj = $item;
               
               return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
           
       } else {
           $message = "Couldn't create Address";
           $this->status = '504';
          return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
       }
   }
   public function update(Request $request)
   {
          $itemObj=null;
          $mess=null;
          $validator = Validator::make($request->all(), [
           'delivery_area' => ['required', 'string', 'max:255'],
           'street' => 'required',           
           ]);
      if ($validator->fails()) {
         
           $bb = $validator->errors()->toArray();
          foreach ($bb as $k => $v) {
              $mess[$k]=$v;
          }
         //  $ErrorMess= json_encode($mess);
           
          return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
      }
          $item =CustomerAddress::find($request->id);
         
          $item->delivery_area = $request->input('delivery_area');
          $item->building = $request->input('building');
          $item->street =  $request->street;
          $item->flatnumber=$request->flatnumber;
          $item->landmark= $request->landmark;
          
          if ($item->save()) {
               $itemObj = $item;
              
              return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
          
      } else {
          $message = "Couldn't create Address";
          $this->status = '504';
         return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
      }
  }
}
