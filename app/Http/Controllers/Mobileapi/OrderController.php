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
            $itemObj=null;
            $mess=null;
            $validator = Validator::make($request->all(), [
            'customer_address_id' => ['required'],
            'discount_id' => 'required',
            'payment_id' => 'required',
            'total'=>'required'
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
            $gifts =BoxesOrder::where('customer_id',$request->user()->id)->where('order_customer_id',0)->get(); 
            $item =new Order();
            $item->slack = $this->generate_slack("orders");
            $item->order_number = Str::random(6);
            $item->customer_id =$request->user()->id;
            $item->customer_address=$request->customer_address_id;
            $item->store_level_discount_code_id= $request->discount_id;
            $item->payment_method_id=$request->payment_id;
            $item->total_order_amount=$request->total;
            $item->country_id= $request->header('country');
            if ($item->save()) {
                $itemObj = $item;
                 foreach($products as $product)
                 {
                    $iproduct=Product::find($product->product_id);
                    if($iproduct){
                    $item =new OrderProduct();
                    $item->slack = $this->generate_slack("order_products");
                    $item->order_id = $itemObj->id;
                    $item->product_id =$iproduct->id;
                    $item->product_slack=$iproduct->slack;
                    $item->product_code= $iproduct->product_code;
                    $item->name=$iproduct->name_en;
                    $item->quantity=$product->quantity;
                    $item->purchase_amount_excluding_tax=$iproduct->purchase_amount_excluding_tax;
                    $item->sale_amount_excluding_tax=$iproduct->sale_amount_excluding_tax;
                    $item->discount_code_id=$iproduct->discount_code_id;
                    $item->sub_total_purchase_price_excluding_tax=$iproduct->purchase_amount_excluding_tax;
                    $item->sub_total_sale_price_excluding_tax=$iproduct->sale_amount_excluding_tax;
                    if($iproduct->sale_amount_excluding_tax!='0.00')
                    $total1=$product->quantity*$iproduct->sale_amount_excluding_tax;
                    else
                    $total1=$product->quantity*$iproduct->purchase_amount_excluding_tax;
                    $item->total_amount=$total1;
                    $iproduct->quantity=($iproduct->quantity)-($product->quantity);
                    $iproduct->save();
                    $item->save();
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
 
                
                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            
        } else {
            $message = "Couldn't add order";
            $this->status = '504';
        return response()->json(['status'=>false,'msg' => $message,'data'=>$itemObj],  $this->status);
        }
    }
}
