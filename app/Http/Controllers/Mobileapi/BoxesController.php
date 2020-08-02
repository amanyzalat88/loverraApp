<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Product;
use App\Models\Mobile\Boxes;
use App\Models\Mobile\BoxesCard;
use App\Models\Mobile\BoxesColor;
use App\Models\Mobile\BoxesOrder;
use App\Models\Mobile\BoxesOrderProducts;
use Illuminate\Support\Str;
use App\Http\Resources\ApiBoxesCardResource;
use App\Http\Resources\ApiBoxesResource;
use App\Http\Resources\ApiBoxesColorResource;
use DB;
class BoxesController extends Controller
{
    public $successStatus = 200;

    public function boxes(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
            }
            if($request->count)
            $Boxes= Boxes::where('count',$request->count)->paginate($count); 
            else
		  $Boxes= Boxes::paginate($count);  
		 
        if ($Boxes->count()>0) {
           
             $result=ApiBoxesResource::collection($Boxes);
            
             $data= [
                    'total' => $Boxes->total(),
                    'count' => $Boxes->count(),
                    'per_page' => intval($Boxes->perPage()),
                    'current_page' => $Boxes->currentPage(),
                    'total_pages' => $Boxes->lastPage(),
                    'items' =>$result,
                    
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Boxes yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function color(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
            }
            
		  $Boxes=  BoxesColor::paginate($count);  
		 
        if ($Boxes->count()>0) {
           
             $result=ApiBoxesColorResource::collection($Boxes);
            
             $data= [
                    'total' => $Boxes->total(),
                    'count' => $Boxes->count(),
                    'per_page' => intval($Boxes->perPage()),
                    'current_page' => $Boxes->currentPage(),
                    'total_pages' => $Boxes->lastPage(),
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Boxes yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }
    public function cards(Request $request)
    {
         $data=null;
		 $message='';
		  $count=20;
			if($request->items_num)
			{
				$count=$request->items_num; 
            }
            
		  $Boxes=   BoxesCard::paginate($count);  
		 
        if ($Boxes->count()>0) {
           
             $result=ApiBoxesCardResource::collection($Boxes);
            
             $data= [
                    'total' => $Boxes->total(),
                    'count' => $Boxes->count(),
                    'per_page' => intval($Boxes->perPage()),
                    'current_page' => $Boxes->currentPage(),
                    'total_pages' => $Boxes->lastPage(),
                    'items' =>$result,
            ];
       			  
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
        } else {
            $message = "Not Boxes yet ";
			
            return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
        }
    }

    public function create(Request $request)
    {
         $data=null;
         $message='';
         $item =new BoxesOrder();
         $item->customer_id = $request->user()->id;
         $item->box_id = $request->box_id;
         $item->color_id = $request->color_id;
         $item->card_id = $request->card_id;
         $item->message = $request->message;
         $item->save();
         $order_id= $item->id;
         $products=$request->products;
         
         foreach($products as $product)
         {
            $itemP =new BoxesOrderProducts();
            $itemP->order_id=$order_id;
            $itemP->product_id=$product["product_id"];
            $itemP->save();    
         }
         $message="";
         return response()->json(['status'=>true,'msg' => $message,'data'=>$item], $this->successStatus);
     }
            
     public function delete(Request $request)
     {
          $data=null;
          $message='';
          if($request->id)
          {
          $item =BoxesOrder::find($request->id);
          if($item)
          { if ($item->delete()) {
            BoxesOrderProducts::where('order_id',$request->id)->delete();
            $message="deleted gift";
                          
           return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
       } else {
           $message =  "item gift not found";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }

          }
          else{
            $message="Can't found  Gift ";
                              
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
          }
        }
          else{
            $message="Can't found  Gift ";
                              
            return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
          }
         
      }
}
