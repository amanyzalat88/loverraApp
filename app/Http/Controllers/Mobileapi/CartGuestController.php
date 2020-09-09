<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Product;
use App\Models\Mobile\Cart;
use Illuminate\Support\Str;
use App\Models\Mobile\BoxesOrder;
use App\Models\Mobile\OrderProduct;
use App\Http\Resources\ApiCartResource;
use App\Http\Resources\ApiBoxesOrderResource;
use DB;
class CartGuestController extends Controller
{
    public $successStatus = 200;

    public function count(Request $request)
    {
        
         $data=null;
         $message='';
         $validator = Validator::make($request->all(), [
            'guest_id' => ['required', 'numeric'],
             
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
       }

            $total['count'] =Cart::where('customer_id',$request->guest_id)->count();  
            $total['count']+= BoxesOrder::where('customer_id',$request->guest_id)->where('order_customer_id',0)->count();
            if($total['count']>0){
           return response()->json(['status'=>true,'msg' => $message,'data'=>$total], $this->successStatus);
       } else {
           $message = "Not cart empty  ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    
    }
    //add 
    public function index(Request $request)
    {
        
         $data=null;
         $message='';
         $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'numeric'],
             'quantity'=>['required']
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
       }
         $prod= Product::find($request->product_id);
         if($prod)
         {
        if($prod->quantity<$request->quantity)
        {
           $lang=  $request->header('lang');
                if(!$lang)
                    $lang='ar';
           $message = $lang=='ar'?'الكمية المطلوبة من هذا المنتج غير متاحه':'The quantity required for this product is not available';
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
        else{
            $rows=0;
            if($request->guest_id)
            {
                $guest_id=$request->guest_id;
         $items =Cart::where('customer_id',$guest_id)->where('product_id',$request->product_id)->first();  
         
        
         
         if(!$items)
         { 
            $item =new Cart();
            $item->product_id = $request->product_id;
            $item->customer_id = $guest_id;
            if($request->quantity)
              $item->quantity = $request->quantity;
            else
              $item->quantity =1; 
            $item->save();
            $rows++;
         }
         else{
            if($request->quantity=="0")
            {
                $items->delete();
                $message = "Product Remove";
                return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
            }
           else {
                if($request->quantity>=1)
                    $quantity =$request->quantity;
                if(!$request->quantity)
                    $quantity= $items->quantity+1;
                DB::table('carts')
                ->where('customer_id', $guest_id)
                ->where('product_id',$request->product_id)
                ->update(['quantity' =>  $quantity]);
                $rows++;
            }
           
         }
        } else{
            $guest_id=rand(0,100000);
            $item =new Cart();
            $item->product_id = $request->product_id;
            $item->customer_id = $guest_id;
            if($request->quantity)
              $item->quantity = $request->quantity;
            else
              $item->quantity =1; 
            $item->save();
            $rows++;
                    
        }
       
        if ($rows>0) {
            $total['customer_id']=(int)$guest_id;
            $total['count'] =Cart::where('customer_id',$guest_id)->count();  
            $total['count']+= BoxesOrder::where('customer_id',$guest_id)->where('order_customer_id',0)->count();
           return response()->json(['status'=>true,'msg' => $message,'data'=>$total], $this->successStatus);
       } else {
           $message = "Not cart empty  ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }
    }else{
        $message = "Not Product not found  ";
           
        return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
    }
    }
  
    public function show(Request $request)
    {
         $data=null;
         $message='';
         $total=0;
         $totals=0;
         $validator = Validator::make($request->all(), [
            'guest_id' => ['required', 'numeric'],
             
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
       }
         $item =Cart::where('customer_id',$request->guest_id)->get();  
         $gifts =BoxesOrder::where('customer_id',$request->guest_id)->where('order_customer_id',0)->get(); 
        
         $j=0;
        if ($item->count()>0 ) {
            $j=1;
            $products=ApiCartResource::collection($item);
            $Finalproducts=json_decode((json_encode($products)));
            foreach($Finalproducts as $i)
            {
                if($i->sale)
                $total+=$i->quantity*$i->sale;
                else
                $total+=$i->quantity*$i->price;
            }

           
          
            $result["products"]=$products;
            
         
       }
       if ($gifts->count()>0 ) {
        $j=2;
        
        $ggifts=ApiBoxesOrderResource::collection($gifts);
        $Finalgifts=json_decode((json_encode($ggifts)));
        
        foreach($Finalgifts as $g)
        {
           
             $totals+=(double)$g->price;
        }
      
        $result["gifts"]=$ggifts;
                      
      
   }if($j>0)
   {
    $result["total"]=$total+$totals;
    return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
   }
       else {
           $message = "cart empty ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }
    public function delete(Request $request)
    {
        
         $data=null;
         $message='';
         $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'numeric'],
            'guest_id' => ['required', 'numeric'],
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
       }
          
         $item =Cart::where('customer_id',$request->guest_id)->where('product_id',$request->product_id)->first();
         if ($item) {
            
           if ($item->delete()) {
            
            $message="deleted Product";
                          
           return response()->json(['status'=>true,'msg' => $message,'data'=>$item], $this->successStatus);
       } else {
           $message =  "item on cart not found";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
      }
      else{
        $message="Can't found  Product ";
                          
        return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
      }
    }
}
