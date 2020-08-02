<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobile\Category;
use App\Models\Mobile\Product;
use App\Http\Resources\ApiCategoryResource;
use App\Http\Resources\ApiProductResource;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
		  $cats= Category::where('status',1)->paginate($count);  
		 
        if ($cats->count()>0) {
           
             $result=ApiCategoryResource::collection($cats);
            
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
            $message = "Not Category yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
   
	
   public function getSubCategory(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
			}
	   
	     $cats=Category::where('parent',$request->parent)->where('status',1)->paginate($count);

	 
        if ($cats->count()>0) {
			$result=ApiCategoryResource::collection($cats);
            $items=$cats->items();
            $in='';
                for($i=1;$i<$cats->count();$i++)
                    {
                        $in.=$items[$i]->id.',';
                    }
                    $in= rtrim($in, ',');
                    
            $product= Product::whereIn('category_id',[$in])->where('status',1)->paginate($count);
            $product=ApiProductResource::collection($product);
            $dataP= [
                'total' => $product->total(),
                'count' => $product->count(),
                'per_page' => intval($product->perPage()),
                'current_page' => $product->currentPage(),
                'total_pages' => $product->lastPage(),
                'items'=>$product,    
             ];
             $data= [
                    'total' => $cats->total(),
                    'count' => $cats->count(),
                    'per_page' => intval($cats->perPage()),
                    'current_page' => $cats->currentPage(),
                    'total_pages' => $cats->lastPage(),
                    'items' =>$result,
                    'product'=>$dataP,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Sub Category yet  ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
   public function getMainCategory(Request $request)
    {
         $data=null;
		 $message='';
		 
		 
		  $cats= Category::MainCategories();  
		  $result=ApiCategoryResource::collection($cats);
        if ($cats) {
              
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
        } else {
            $message = "Not Category yet  ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
}
