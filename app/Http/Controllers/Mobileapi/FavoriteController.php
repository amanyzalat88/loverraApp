<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Favorite;
use App\Models\Mobile\Product;
use Illuminate\Support\Str;
use App\Http\Resources\ApiFavoriteResource;

class FavoriteController extends Controller
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
           $item =Favorite::where('customer_id',$request->user()->id)->paginate($count); 
        
       if ($item->count()>0) {
            $result=ApiFavoriteResource::collection($item);
            $data= [
                   'total' => $item->total(),
                   'count' => $item->count(),
                   'per_page' => intval($item->perPage()),
                   'current_page' => $item->currentPage(),
                   'total_pages' => $item->lastPage(),
                   'items' =>$result,
           ];
                    
           return response()->json(['status'=>true,'msg' => $message,'data'=>$data], $this->successStatus);
       } else {
           $message = "Not Favorite yet ";
           
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }
    public function store(Request $request)
    {
            $itemObj=null;
		    $mess=null;
           
        $item =Favorite::where('customer_id',$request->user()->id)->where('product_id',$request->product_id)->count();
        if ($item==0) {
            $Favorite =new Favorite();
            //$item->id = $request->user()->id;
            $Favorite->product_id = $request->product_id;
            $Favorite->customer_id = $request->user()->id;
         
            if ($Favorite->save()) {
                $itemObj = $Favorite;
                   
                return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
            }
        } else {
            $mess = "Product Added Before";
            $this->status = '403';

            return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj],  $this->status);
        }
    
}
    
public function delete(Request $request)
{
        $itemObj=null;
        $mess=null;
       
    $item =Favorite::where('customer_id',$request->user()->id)->where('product_id',$request->product_id)->first();
    if ($item) {
       
        if ($item->delete()) {
          $mess="deleted favorite";
            return response()->json(['status'=>true,'msg' => $mess,'data'=>$itemObj], $this->successStatus);
        }
    else {
        $mess = "can't deleted favorite";
        $this->status = '403';

        return response()->json(['status'=>false,'msg' => $mess,'data'=>$itemObj],  $this->status);
    }

    }
}
}
