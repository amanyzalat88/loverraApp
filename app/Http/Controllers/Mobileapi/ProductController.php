<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobile\Product;
use App\Models\Mobile\Category;
use App\Http\Resources\ApiProductResource;
use Illuminate\Support\Str;

class ProductController extends Controller
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
            $cats=Category::find($request->catId);
           //var_dump($cats);
            
            //if($cats->parent==0&& $cats->label_ar=='الكل')
            if($cats->parent==0)
            {
                $catss=Category::where('parent',$cats->id)->where('status',1)->get();
                $in='';
               
                for($i=1;$i<$catss->count();$i++)
                    {
                        $in.=$catss[$i]->id.',';
                    }
                    $in= rtrim($in, ',');
                    $in=explode(',' ,$in);
            $product= Product::whereIn('category_id',$in)->where('status',1)->paginate($count);
            }else{

		     $product= Product::where('category_id',$request->catId)->where('status',1)->paginate($count);  
            }
        if ($product->count()>0) {
			 $result=ApiProductResource::collection($product);
             $data= [
                    'total' => $product->total(),
                    'count' => $product->count(),
                    'per_page' => intval($product->perPage()),
                    'current_page' => $product->currentPage(),
                    'total_pages' => $product->lastPage(),
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Products yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function detail(Request $request)
    {
         $data=null;
		 $message='';
         $product= Product::where('id',$request->id)->get(); 
             
        if ($product) {
            $result=ApiProductResource::collection($product);  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
        } else {
            $message = "Not Products yet ";	
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function search(Request $request)
    {
         $data=null;
         $message='';
         $category_id=$request->category_id;
         if($category_id==0){
         $product= Product::where('name_ar','like', '%' . $request->word . '%')
         ->orWhere('name_en','like', '%' . $request->word . '%')->get();
         }else{
            $product= Product::where('name_ar','like', '%' . $request->word . '%')
            ->Where('category_id',$category_id)
            ->orWhere('name_en','like', '%' . $request->word . '%')
            ->Where('category_id',$category_id)->get();
         }
         
          
        if ($product->count()>0) {
            $result=ApiProductResource::collection($product);  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
        } else {
            $message = "Not Products yet ";	
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function related(Request $request)
    {
         $data=null;
		 $message='';
		  $count=5;
			if($request->items_num)
			{
				$count=$request->items_num; 
            }
           $pro= Product::find($request->id);
           if($pro)
           {
            
		     $product= Product::where('category_id',$pro->category_id)->where('status',1)->inRandomOrder()->limit($count)->get();  
            
        if ($product->count()>0) {
			 $result=ApiProductResource::collection($product);
        /* $data= [
                    'total' => $product->total(),
                    'count' => $product->count(),
                    'per_page' => intval($product->perPage()),
                    'current_page' => $product->currentPage(),
                    'total_pages' => $product->lastPage(),
                    'items' =>$result,
            ];*/
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
        } else {
            $message = "Not Products yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    } else {
        $message = "Not Product yet ";
        
        return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
    }
    }
   public function latest(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		  $product= Product::orderBy('id', 'DESC')->take(10)->get();  
		 
        if ($product->count()>0) {
			 $result=ApiProductResource::collection($product);
             $data= [
                   
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Products yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function best_sales(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		  $product= Product::Join('order_products', 'order_products.product_id', '=', 'products.id')
          ->selectRaw('products.*, count(order_products.product_id) as Total')
          ->groupBy('order_products.product_id')
          ->orderBy('Total', 'DESC')
          ->take(10)->get(); 
          
        if ($product->count()>0) {
			 $result=ApiProductResource::collection($product);
             $data= [
                   
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Products yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function offers(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		  $product= Product::where('sale_amount_excluding_tax','!=','0.00')->take(10)->get();  
		 
        if ($product->count()>0) {
			 $result=ApiProductResource::collection($product);
             $data= [
                   
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Products yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
   public function soldout(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
		  $product= Product::where('soldout',1)->take(10)->get();  
		 
        if ($product->count()>0) {
			 $result=ApiProductResource::collection($product);
             $data= [
                   
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Products yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
   
}
