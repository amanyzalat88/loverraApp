<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobile\Order;
use App\Models\Mobile\Cart;
use App\Models\Mobile\BoxesOrder;
use App\Models\Mobile\OrderProduct;
use App\Models\Mobile\Product;
use App\Http\Resources\ApiOrderResource;
use App\Http\Resources\ApiCartResource;
use App\Http\Resources\ApiBoxesOrderResource;
use App\Models\Mobile\Discountcode as DiscountcodeModel;
use App\Models\Mobile\Customer;
use App\Models\Mobile\Payment;
use Illuminate\Support\Str;
use Validator;
use App\Models\Mobile\Store;
use DB;
use Illuminate\Support\Facades\Auth;
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
    public function details(Request $request)
    {
            
        
        $data=null;
        $message='';
        $total=0;
        $totals=0;
        $tot_discount=0;
        $item =Cart::where('customer_id',$request->user()->id)->get();  
        $gifts =BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->get(); 
       
        $j=0;
       if ($item->count()>0 ) {
           $j=1;
           $products=ApiCartResource::collection($item);
           $Finalproducts=json_decode((json_encode($products)));
           foreach($Finalproducts as $i)
           {
               if($i->discount!='0.00'){
                $total+=$i->quantity*$i->discount; 
                if($i->sale)
                $tot_discount+=number_format($i->sale-$i->discount,2);
                else
                $tot_discount+=number_format($i->price-$i->discount,2);
               }
                else{
               if($i->sale)
               $total+=$i->quantity*$i->sale;
               else
               $total+=$i->quantity*$i->price;
                }
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
    $tot=$total+$totals;
    $dicount_id= 0;
    $discount_discount_code=0;
    $discount_discount_percentage=0;
      if($request->discount_id)
      {
        $discount= DiscountcodeModel::where('id',$request->discount_id)->where('status',1)->first();
		  
        if($discount)
        {
			if($discount->discount_type==3)
			{
            $totInvoce= $tot-($tot*$discount->discount_percentage);
            $tot_discount=number_format($tot-$totInvoce,2);
            $tot=$totInvoce;
			}
        
       $dicount_id= $discount->id;
       $discount_discount_code=$discount->discount_code;
       $discount_discount_percentage=$discount->discount_percentage;
			}
    }
     
      $shipping=Store::select('shipping','free_shipping')->first();
      if($tot>$shipping->free_shipping)
      $shipping=0;
      else
      $shipping=$shipping->shipping;

      $result["total"]=$tot+$tot_discount;
      $result["total_after_discount"]=$tot;
      $result["total_discount"]=$tot_discount;
      $result["shipping"]=$shipping;
      $result["all"]=$tot+$shipping;
      $result['discount_id']=$dicount_id;  
      $result['discount_code']=$discount_discount_code;
      $result['discount_percentage']=$discount_discount_percentage;


   return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
  }
      else {
          $message = "Couldn't found order";
          
          return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
      }
    }
    public function add(Request $request)
    {
		
        $validator = Validator::make($request->all(), [
            'customer_address_id' => ['required'],
            
            'payment_id' => 'required'
            ]);
        if ($validator->fails()) {
        
            $bb = $validator->errors()->toArray();
            foreach ($bb as $k => $v) {
                $mess[$k]=$v;
            }
        //  $ErrorMess= json_encode($mess);
            
            return response()->json(['status'=>false,'msg' => $mess,'data'=>null], 503);
        }
		  $products =Cart::where('customer_id',$request->user()->id)->get(); 
		if($products->count()>0)
		{
			 $itemObj=null;
            $mess=null;
        if($request->payment_id==1)
        {
           
           
            $products =Cart::where('customer_id',$request->user()->id)->get(); 
			
            if($products->count()>0)
            {
				$discountTotal=null;
            $gifts =BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->get(); 
            $Customer =Customer::find($request->user()->id);
            $item =new Order();
            $item->slack = $this->generate_slack("orders");
            $item->order_number = Str::random(6);
            $item->customer_id =$request->user()->id;
            $item->customer_address=$request->customer_address_id;
            $item->store_level_discount_code_id= $request->discount_id;
            $item->payment_method_id=$request->payment_id;
            $item->customer_email=$Customer->email;
            $item->customer_phone=$Customer->phone;
            $item->country_id= $request->header('country');
            if ($item->save()) {
                $itemObj = $item;
                 foreach($products as $product)
                 {
                    $iproduct=Product::find($product->product_id);
                    if($iproduct){
                    $OrderProduct =new OrderProduct();
                    $OrderProduct->slack = $this->generate_slack("order_products");
                    $OrderProduct->order_id = $itemObj->id;
                    $OrderProduct->product_id =$iproduct->id;
                    $OrderProduct->product_slack=$iproduct->slack;
                    $OrderProduct->product_code= $iproduct->product_code;
                    $OrderProduct->name=$iproduct->name_en;
                    $OrderProduct->quantity=$product->quantity;
                    $OrderProduct->purchase_amount_excluding_tax=$iproduct->purchase_amount_excluding_tax;
                    $OrderProduct->sale_amount_excluding_tax=$iproduct->sale_amount_excluding_tax;
                    if($product->discount!=='0.00')
                    {
                        
                        $discount= DiscountcodeModel::find($iproduct->discount_code_id);
                        $OrderProduct->discount_code_id=$discount->id;
                        $OrderProduct->discount_code=$discount->discount_code;
                        $OrderProduct->discount_percentage=$discount->discount_percentage;
                        $OrderProduct->discount_amount=$product->discount;
                        $OrderProduct->total_after_discount=$product->quantity*$product->discount;
                        $OrderProduct->total_amount=$product->quantity*$product->discount;
                    }else{
                    $OrderProduct->sub_total_purchase_price_excluding_tax=$iproduct->purchase_amount_excluding_tax;
                    $OrderProduct->sub_total_sale_price_excluding_tax=$iproduct->sale_amount_excluding_tax;
                    if($iproduct->sale_amount_excluding_tax!='0.00')
                    $OrderProduct->total_amount=$product->quantity*$iproduct->sale_amount_excluding_tax;
                    else
                    $OrderProduct->total_amount=$product->quantity*$iproduct->purchase_amount_excluding_tax;
                    }
                    $total=0;
                    $iproduct->quantity=($iproduct->quantity)-($product->quantity);
                    $iproduct->save();
                    $OrderProduct->save();
						if($request->discount_id)
						{
                    $discountTotal= DiscountcodeModel::find($request->discount_id);
							if($discountTotal)
							{
                    if($discountTotal->discount_type==3)
                    {
                        $updateTotal =Order::find($item->id);
                        $updateTotal->store_level_discount_code_id=$discountTotal->id;
                        $updateTotal->store_level_discount_code=$discountTotal->discount_code;
                        $updateTotal->store_level_total_discount_percentage=$discountTotal->discount_percentage;
                        $total=DB::table('order_products')->where('order_id',$item->id)->sum('total_amount');
                        $tot=$total-($total*$discountTotal->discount_percentage);
                        $updateTotal->total_discount_amount=number_format($total-$tot,2);
                        $updateTotal->total_after_discount=$tot;
                        $updateTotal->total_order_amount=$tot;
                        $updateTotal->save();
                        $itemObj["total"]=$tot;
                        $itemObj["total_after_discount"]=$tot;
                        $itemObj["total_discount"]=number_format($total-$tot,2);
                        $total=$tot;
                    }
                    else{
                       
                        $updateTotal =Order::find($item->id);
                        $updateTotal->store_level_discount_code_id=$discountTotal->id;
                        $updateTotal->store_level_discount_code=$discountTotal->discount_code;
                        $updateTotal->store_level_total_discount_percentage=$discountTotal->discount_percentage;
                        $total_after_discount=DB::table('order_products')->where('order_id',$item->id)->sum('total_amount');
                        $total_discount=DB::table('order_products')->where('order_id',$item->id)->sum('discount_amount');
                        $updateTotal->total_discount_amount=$total_discount;
                        $updateTotal->total_after_discount=$total_after_discount;
                        $updateTotal->total_order_amount=$total_after_discount;
                        $updateTotal->save();
                        $total=$total_after_discount;
                        $itemObj["total"]=$total_after_discount;
                        $itemObj["total_after_discount"]=$total_after_discount;
                        $itemObj["total_discount"]=$total_discount;
                    }
						}
						}else{
						 $updateTotal =Order::find($item->id);
                        $updateTotal->store_level_discount_code_id=0;
                        $updateTotal->store_level_discount_code=null;
                        $updateTotal->store_level_total_discount_percentage=0.00;
                        $total_after_discount=DB::table('order_products')->where('order_id',$item->id)->sum('total_amount');
                        //$total_discount=DB::table('order_products')->where('order_id',$item->id)->sum('discount_amount');
                        $updateTotal->total_discount_amount=0;
                        $updateTotal->total_after_discount=$total_after_discount;
                        $updateTotal->total_order_amount=$total_after_discount;
                        $updateTotal->save();
                        $total=$total_after_discount;
                        $itemObj["total"]=$total_after_discount;
                        $itemObj["total_after_discount"]=$total_after_discount;
                        $itemObj["total_discount"]=0;
						
						}
                   
                     
                }
                 }
                 if($gifts->count()>0)
                 {
                    $iproducts=BoxesOrderProducts::where('order_id',$gift->id)->get();
                    if($iproducts->count()>0){
                    foreach($iproducts as $iproduct)
                    {
                        $ipro=Product::find($iproduct->product_id);
                        if($ipro){
                        $ipro->quantity=($ipro->quantity)-1;
                        $ipro->save();
                        }
                    }
                }
                    DB::table('boxes_orders')->where('customer_id',$request->user()->id)->where('order_customer_id',0)->update(array('order_customer_id' => $itemObj->id));
                 }
                 
                 
                 
                 DB::table('carts')->where('customer_id',$request->user()->id)->delete();
 
            $shipping=Store::select('shipping','free_shipping')->first();
           
          if($total>$shipping->free_shipping)
          $shipping=0;
          else
          $shipping=$shipping->shipping;
            
            $itemObj["shipping"]=$shipping;
            $itemObj["all"]=$total+$shipping;
				if($discountTotal)
				{
            $itemObj['discount_id']=$discountTotal->id;  
            $itemObj['discount_code']=$discountTotal->discount_code;
            $itemObj['discount_percentage']=$discountTotal->discount_percentage;
				}else{
					 $itemObj['discount_id']=0;  
            $itemObj['discount_code']=0;
            $itemObj['discount_percentage']=0;
				}

                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
              
        }
    }
}  else{ 
        $Payment = Payment::where('txnId', $request->txnId)->where('order_id', 0)->first();
        if($Payment)
        {
            $itemObj=null;
            $mess=null;
            $validator = Validator::make($request->all(), [
           
            'txnId'=>'required'
            ]);
        if ($validator->fails()) {
        
            $bb = $validator->errors()->toArray();
            foreach ($bb as $k => $v) {
                $mess[$k]=$v;
            }
        //  $ErrorMess= json_encode($mess);
            
            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj], 503);
        }
        
            $products =Cart::where('customer_id',$request->user()->id)->get();  
            if($products->count()>0)
            {
                $discountTotal=null;
            $gifts =BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->get(); 
            $Customer =Customer::find($request->user()->id);
            $item =new Order();
            $item->slack = $this->generate_slack("orders");
            $item->order_number = Str::random(6);
            $item->customer_id =$request->user()->id;
            $item->customer_address=$request->customer_address_id;
            $item->store_level_discount_code_id= $request->discount_id;
            $item->payment_method_id=$request->payment_id;
            $item->customer_email=$Customer->email;
            $item->customer_phone=$Customer->phone;
            $item->country_id= $request->header('country');
            if ($item->save()) {
                $itemObj = $item;
                 foreach($products as $product)
                 {
                    $iproduct=Product::find($product->product_id);
                    if($iproduct){
                    $OrderProduct =new OrderProduct();
                    $OrderProduct->slack = $this->generate_slack("order_products");
                    $OrderProduct->order_id = $itemObj->id;
                    $OrderProduct->product_id =$iproduct->id;
                    $OrderProduct->product_slack=$iproduct->slack;
                    $OrderProduct->product_code= $iproduct->product_code;
                    $OrderProduct->name=$iproduct->name_en;
                    $OrderProduct->quantity=$product->quantity;
                    $OrderProduct->purchase_amount_excluding_tax=$iproduct->purchase_amount_excluding_tax;
                    $OrderProduct->sale_amount_excluding_tax=$iproduct->sale_amount_excluding_tax;
                    if($product->discount!=='0.00')
                    {
                        
                        $discount= DiscountcodeModel::find($iproduct->discount_code_id);
                        $OrderProduct->discount_code_id=$discount->id;
                        $OrderProduct->discount_code=$discount->discount_code;
                        $OrderProduct->discount_percentage=$discount->discount_percentage;
                        $OrderProduct->discount_amount=$product->discount;
                        $OrderProduct->total_after_discount=$product->quantity*$product->discount;
                        $OrderProduct->total_amount=$product->quantity*$product->discount;
                    }else{
                    $OrderProduct->sub_total_purchase_price_excluding_tax=$iproduct->purchase_amount_excluding_tax;
                    $OrderProduct->sub_total_sale_price_excluding_tax=$iproduct->sale_amount_excluding_tax;
                    if($iproduct->sale_amount_excluding_tax!='0.00')
                    $OrderProduct->total_amount=$product->quantity*$iproduct->sale_amount_excluding_tax;
                    else
                    $OrderProduct->total_amount=$product->quantity*$iproduct->purchase_amount_excluding_tax;
                    }
                    $total=0;
                    $iproduct->quantity=($iproduct->quantity)-($product->quantity);
                    $iproduct->save();
                    $OrderProduct->save();
						
						if($request->discount_id)
						{
                    $discountTotal= DiscountcodeModel::find($request->discount_id);
							if($discountTotal)
							{
                    if($discountTotal->discount_type==3)
                    {
                        $updateTotal =Order::find($item->id);
                        $updateTotal->store_level_discount_code_id=$discountTotal->id;
                        $updateTotal->store_level_discount_code=$discountTotal->discount_code;
                        $updateTotal->store_level_total_discount_percentage=$discountTotal->discount_percentage;
                        $total=DB::table('order_products')->where('order_id',$item->id)->sum('total_amount');
                        $tot=$total-($total*$discountTotal->discount_percentage);
                        $updateTotal->total_discount_amount=number_format($total-$tot,2);
                        $updateTotal->total_after_discount=$tot;
                        $updateTotal->total_order_amount=$tot;
                        $updateTotal->save();
                        $itemObj["total"]=$tot;
                        $itemObj["total_after_discount"]=$tot;
                        $itemObj["total_discount"]=number_format($total-$tot,2);
                        $total=$tot;
                    }
                    else{
                       
                        $updateTotal =Order::find($item->id);
                        $updateTotal->store_level_discount_code_id=$discountTotal->id;
                        $updateTotal->store_level_discount_code=$discountTotal->discount_code;
                        $updateTotal->store_level_total_discount_percentage=$discountTotal->discount_percentage;
                        $total_after_discount=DB::table('order_products')->where('order_id',$item->id)->sum('total_amount');
                        $total_discount=DB::table('order_products')->where('order_id',$item->id)->sum('discount_amount');
                        $updateTotal->total_discount_amount=$total_discount;
                        $updateTotal->total_after_discount=$total_after_discount;
                        $updateTotal->total_order_amount=$total_after_discount;
                        $updateTotal->save();
                        $total=$total_after_discount;
                        $itemObj["total"]=$total_after_discount;
                        $itemObj["total_after_discount"]=$total_after_discount;
                        $itemObj["total_discount"]=$total_discount;
                    }
							}else{
							$updateTotal =Order::find($item->id);
                        $updateTotal->store_level_discount_code_id=0;
                        $updateTotal->store_level_discount_code=null;
                        $updateTotal->store_level_total_discount_percentage=0.00;
                        $total_after_discount=DB::table('order_products')->where('order_id',$item->id)->sum('total_amount');
                       // $total_discount=DB::table('order_products')->where('order_id',$item->id)->sum('discount_amount');
                        $updateTotal->total_discount_amount=0;
                        $updateTotal->total_after_discount=$total_after_discount;
                        $updateTotal->total_order_amount=$total_after_discount;
                        $updateTotal->save();
                        $total=$total_after_discount;
                        $itemObj["total"]=$total_after_discount;
                        $itemObj["total_after_discount"]=$total_after_discount;
                        $itemObj["total_discount"]=0;
							}
						}
                    
                   
                     
                }
                 }
                 if($gifts->count()>0)
                 {
                    $iproducts=BoxesOrderProducts::where('order_id',$gift->id)->get();
                    if($iproducts->count()>0){
                    foreach($iproducts as $iproduct)
                    {
                        $ipro=Product::find($iproduct->product_id);
                        if($ipro){
                        $ipro->quantity=($ipro->quantity)-1;
                        $ipro->save();
                        }
                    }
                }
                    DB::table('boxes_orders')->where('customer_id',$request->user()->id)->where('order_customer_id',0)->update(array('order_customer_id' => $itemObj->id));
                 }
                 $Payment->order_id=$itemObj->id;
                 $Payment->save();
                 
                 
                 DB::table('carts')->where('customer_id',$request->user()->id)->delete();
 
            $shipping=Store::select('shipping','free_shipping')->first();
           
          if($total>$shipping->free_shipping)
          $shipping=0;
          else
          $shipping=$shipping->shipping;
            
            $itemObj["shipping"]=$shipping;
            $itemObj["all"]=$total+$shipping;
				if($discountTotal)
						{
            $itemObj['discount_id']=$discountTotal->id;  
            $itemObj['discount_code']=$discountTotal->discount_code;
            $itemObj['discount_percentage']=$discountTotal->discount_percentage;
				}else{
					  $itemObj['discount_id']=0;  
            $itemObj['discount_code']=0;
            $itemObj['discount_percentage']=0;
				}

                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            
        } else {
            $message = "Couldn't add order";
            $this->status = '504';
        return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
        }
    }else{
        $message = "Cart empty";
        $this->status = '504';
    return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
    }
    }

        else{
            $Payment = Payment::where('txnId', $request->txnId)->first();
            if(!$Payment)
            $message = "Transaction Id Payment not found";
             else 
             $message = "Transaction Id Used Before ";
                $this->status = '404';
            return response()->json(['status'=>false,'msg' => $message,'data'=>null],  $this->status); 
        }
		}
    }else{
		$message = "Cart Is empty ";
		 
            return response()->json(['status'=>false,'msg' => $message,'data'=>null], $this->successStatus);
	}
	}
}
