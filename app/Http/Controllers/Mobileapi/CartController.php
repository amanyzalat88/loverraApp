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
class CartController extends Controller
{
    public $successStatus = 200;

    public function count(Request $request)
    {
        
         $data=null;
         $message='';
        

            $total['count'] =Cart::where('customer_id',$request->user()->id)->count();  
            $total['count']+= BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->count();
            if($total['count']>0){
           return response()->json(['status'=>true,'msg' => $message,'data'=>$total], $this->successStatus);
       } else {
           $message = "Not cart empty  ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    
    }
    public function index(Request $request)
    {
        
         $data=null;
         $message='';
         $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'numeric'],
            //'quantity'=>['required']
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
         $items =Cart::where('customer_id',$request->user()->id)->where('product_id',$request->product_id)->first();  
         $rows=0;
        
         
         if(!$items)
         { 
            $item =new Cart();
            $item->product_id = $request->product_id;
            $item->customer_id = $request->user()->id;
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
                ->where('customer_id', $request->user()->id)
                ->where('product_id',$request->product_id)
                ->update(['quantity' =>  $quantity]);
                $rows++;
            }
           
         }
       
        if ($rows>0) {

            $total['count'] =Cart::where('customer_id',$request->user()->id)->count();  
            $total['count']+= BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->count();
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
    public function reorder(Request $request)
    {
        
         $data=null;
         $message='';
         $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'numeric'],
             
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
       }
         $products=
         OrderProduct::select('products.id','products.name_ar','products.name_en','order_products.quantity','products.quantity as proquantity')
         ->join('products', 'products.id', '=', 'order_products.product_id')
         ->join('orders','orders.id','=','order_products.order_id')->
         where('orders.id',$request->order_id)->where('orders.customer_id',$request->user()->id)
			->where('order_products.order_id',$request->order_id) 
			 ->get();
    if($products->count()>0)
    {
       
         foreach($products as $product)
         {
             
                
                    if($product->quantity>$product->proquantity)
                    {
                    $lang=  $request->header('lang');
                        if(!$lang)
                          $lang='ar';
                    $message.= $lang=='ar'?' الكمية المطلوبة من هذا المنتج '.$product->name_ar.'   غير متاحه ' :'The quantity required for this product '.$product->name_en.' is not available ' ;
                    //return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
                continue;  
                }
                    
                    $items =Cart::where('customer_id',$request->user()->id)->where('product_id',$product->id)->first();  
                    
                    if(!$items)
                    { 
                        $item =new Cart();
                        $item->product_id = $product->id;
                        $item->customer_id = $request->user()->id;
                        if($product->quantity)
                           $item->quantity = $product->quantity;
                        else
                           $item->quantity =1; 
                        $item->save();
                      
                    }
                    else{
                            $quantity= $items->quantity+$product->quantity;
                           
                            DB::table('carts')
                            ->where('customer_id', $request->user()->id)
                            ->where('product_id',$product->id)
                            ->update(['quantity' =>  $quantity]);
                            
                    }
                }
                        $item =Cart::where('customer_id',$request->user()->id)->get(); 
                        $result=ApiCartResource::collection($item); 
                        if($message&&$item->count()==0)                
                    return response()->json(['status'=>false,'msg' => $message,'data'=>$result], $this->successStatus);
               else
               return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
            }
            else{
                $message='الطلب او العضو غير موجودين';
                return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
            }
    }
    public function show(Request $request)
    {
         $data=null;
         $message='';
         $total=0;
         $totals=0;
         $item =Cart::where('customer_id',$request->user()->id)->get();  
         $gifts =BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->get(); 
        
         $j=0;
        if ($item->count()>0 ) {
            $j=1;
            $products=ApiCartResource::collection($item);
            $Finalproducts=json_decode((json_encode($products)));
            $lang=  $request->header('lang');
            if(!$lang)
                $lang='ar';
            foreach($Finalproducts as $i)
            {
                $proQuantity=product::find($i->id);
                if($proQuantity->quantity==0)
                {
                    $message.=$lang=='ar'?'المنتج '.$proQuantity->name_ar.' غير متوفر حاليا ' :'Product '.$proQuantity->name_en.' is  not available now';
                }else{
                    if($proQuantity->quantity>=$i->quantity)
                    {
                        
                        if($i->sale)
                            $total+=$i->quantity*$i->sale;
                        else
                            $total+=$i->quantity*$i->price;

                    }else{

                        if($i->sale)
                            $total+=$proQuantity->quantity*$i->sale;
                        else
                            $total+=$proQuantity->quantity*$i->price;
                            $i->quantity=(double)$proQuantity->quantity;
                            $message.=$lang=='ar'?'قفط الكمية  '.$proQuantity->quantity.' من المنتج '.$proQuantity->name_ar.' متاحة ' :'Only '.$proQuantity->quantity .' quantity of  product '.$proQuantity->name_en.' is available  ';
                    }
                }
            }
        
           
            $result["products"]=$Finalproducts;
            
         
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
             
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
          //  $ErrorMess= json_encode($mess);
            
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
       }
          
         $item =Cart::where('customer_id',$request->user()->id)->where('product_id',$request->product_id)->first();
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
