<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobile\Order;
use App\Http\Resources\ApiOrderResource;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public $successStatus = 200;
 
    public function show(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		 
		  $product= Order::where('customer_id',$request->user()->id)->where('id',$request->id)->get();  
		 
        if ($product->count()>0) {
			 $result=ApiOrderResource::collection($product);
             
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
        } else {
            $message = "Not Order Detial yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function index(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		 
		  $product= Order::where('customer_id',$request->user()->id)->paginate($count);
		 
        if ($product->count()>0) {
			 $result=ApiOrderResource::collection($product);
             $data= [
                'total' => $product->total(),
                'count' => $product->count(),
                'per_page' => intval($product->perPage()),
                'current_page' => $product->currentPage(),
                'total_pages' => $product->lastPage(),
                'items' =>$result
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Order Detial yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
  
}
