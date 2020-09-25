<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Mobile\Discountcode as DiscountcodeModel;
use Validator;
use App\Models\Mobile\Cart;
use App\Models\Mobile\Product;
use App\Models\Mobile\Category;
use App\Models\Mobile\Order as OrderModel;
use App\Http\Resources\ApiDiscountcodeResource;
use App\Http\Resources\ApiCartResource;
use App\Models\Mobile\Store;
use DB;

class DiscountCodeController extends Controller
{
    public $successStatus = 200;
    public function removeCode(Request $request)
    {
        $data=null;
        $message='';
       
        Cart::where('customer_id',$request->user()->id)->update(array('discount'=>'0.00'));
        $message='This code Removed';
        $total=0;
        $tot_discount=0;
        $item =Cart::where('customer_id',$request->user()->id)->get();  
        if ($item->count() >0 ) {
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
       }
        
          $shipping=Store::select('shipping','free_shipping')->first();
          if($total>$shipping->free_shipping)
          $shipping=0;
          else
          $shipping=$shipping->shipping;
          $data["total"]=$total;
            $data["total_after_discount"]=0;
            $data["total_discount"]=0;
            $data["shipping"]=$shipping;
            $data["all"]=$total+$shipping;
            $data['discount_id']=0;  
            $data['discount_code']=0;
            $data['discount_percentage']=0;

        return response()->json(['status'=>true,'msg' => $message,'data'=>$data], 200);
    }
    public function check(Request $request)
    {
        $data=null;
        $message='';
        $validator = Validator::make($request->all(), [
            'discount_code' => ['required'],
             
            ]);
       if ($validator->fails()) {
          
            $bb = $validator->errors()->toArray();
           foreach ($bb as $k => $v) {
               $mess[$k]=$v;
           }
           return response()->json(['status'=>false,'msg' => $mess,'data'=>$data], 503);
        }
        $checkCart=Cart::where('customer_id',$request->user()->id)->count();
        if($checkCart >0)
        {
		 $i=0;
        $discount= DiscountcodeModel::where('discount_code',$request->discount_code)->where('status',1)->first();
        if($discount)
        {
        
        if($discount->discount_num)
        {
           
        $count=OrderModel::where('store_level_discount_code_id',$discount->id)->count();
         
        if($discount->discount_num<=$count)
        {
            $message='This Code expired ';
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
        }
       
    }
 
    if($discount->discount_from!='0000-00-00 00:00:00'&&!is_null($discount->discount_from))
    {
        $currentDate = date('Y-m-d');
        $currentDate = date('Y-m-d', strtotime($currentDate));
        $startDate = date('Y-m-d',  strtotime($discount->discount_from));
        $endDate = date('Y-m-d', strtotime($discount->discount_to));
       
        if(!(($startDate<=$currentDate)&&($endDate>=$currentDate)))
        {  
            $message='This Code expired Date ';
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
        }
    }
        if( OrderModel::where('customer_id',$request->user()->id)->where('store_level_discount_code_id',$discount->id)->count()>0)
        {
             $message='This Code Used Before'; 
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
        }
        if($discount->discount_type==3)
        {
            
            Cart::where('customer_id',$request->user()->id)->update(array('discount'=>'0.00'));
        }
        if($discount->discount_type==1)
        {
            $products =Cart::select('product_id')->where('customer_id',$request->user()->id)->get(); 
            if($products->count()>0){
             $array=$products->toArray();
             foreach ($array as $k => $v) {   
                $mess[$k]=$v['product_id'];
            }  
        $discount_prod=Product::where('discount_code_id',$discount->id)->whereIn('id',$mess)->get();
        if($discount_prod->count()>0)
            {
                foreach($discount_prod as $product)
                {
                    if($product->sale_amount_excluding_tax)
                     $price=$product->sale_amount_excluding_tax-($product->sale_amount_excluding_tax*$discount->discount_percentage);
                    else
                     $price=$product->purchase_amount_excluding_tax-($product->purchase_amount_excluding_tax*$discount->discount_percentage);
                    Cart::where('customer_id',$request->user()->id)->where('product_id',$product->id)->update(array('discount'=>$price));
                }
            }
            else{
                $message='This code is valid for use in the products';
                return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
            }
            
        }
        else{
            $message='you do not have  products for this code';
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
        }
        }else if($discount->discount_type==2)
        {
            $products =DB::table('category')->select('products.id')->
            Join('products', 'products.category_id', '=', 'category.id')
            ->where('category.discount_code_id',$discount->id)->get(); 
             
             if($products->count()>0){
             
            foreach ($products as $k => $v) {   
               $mess[$k]=$v->id;
           }  
           
           $discount_prod=Cart::where('customer_id',$request->user()->id)->whereIn('product_id',$mess)->get();
                if($discount_prod->count()>0)
                    {

                        foreach($discount_prod as $product)
                        {
                            $prod=product::find($product->product_id);
                            if($prod->sale_amount_excluding_tax)
                            $price=$prod->sale_amount_excluding_tax-($prod->sale_amount_excluding_tax*$discount->discount_percentage);
                            else
                            $price=$prod->purchase_amount_excluding_tax-($prod->purchase_amount_excluding_tax*$discount->discount_percentage);
                            Cart::where('customer_id',$request->user()->id)->where('product_id',$product->product_id)->update(array('discount'=>$price));
                        }
                    }
                    else{
                        $message='This code is valid for use in the section';
                        return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
                    }
                }
                else{
                    $message='This section has not products for this code';
                    return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
                }
        }
        
        $total=0;
        $tot_discount=0;
        $item =Cart::where('customer_id',$request->user()->id)->get();  
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
       }
        if($discount->discount_type==3)
        {
            $tot= $total-($total*$discount->discount_percentage);
            $tot_discount=number_format($total-$tot,2);
            $total=$tot;
        }
          $shipping=Store::select('shipping','free_shipping')->first();
          if($total>$shipping->free_shipping)
          $shipping=0;
          else
          $shipping=$shipping->shipping;
            $data["total"]=$total+$tot_discount;
            $data["total_after_discount"]=$total;
            $data["total_discount"]=$tot_discount;
            $data["shipping"]=$shipping;
            $data["all"]=$total+$shipping;
            $data['discount_id']=$discount->id;  
            $data['discount_code']=$discount->discount_code;
            $data['discount_percentage']=$discount->discount_percentage;

            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "This Code Incorrect ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
        }
    }else{
        $message = "Cart Empty ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], 503);
    }
    }
}
