<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ApiCartProductResource;
use DB;
class BoxesOrder extends Model {
     
    protected $table = 'boxes_orders';
	protected $hidden = [ 'updated_at','created_at'];
    protected $fillable = [ 'id','customer_id','box_id','color_id','card_id','message'];
    public function BoxesName($box_id,$name){ 
        return Boxes::find($box_id)->$name;
        
    }
    public function Color($box_id,$name){ 
       
        return BoxesColor::find($box_id)->$name;
    }
    public function Card($box_id,$name){ 
        
        return BoxesCard::find($box_id)->$name;
    }
    public function BoxesPhoto($box_id){ 
        return Boxes::find($box_id)->photo;
    }
    public function total($id){ 
        $order=BoxesOrder::find($id);
        $prics=Boxes::find($order->box_id)->price+ BoxesColor::find($order->color_id)->price+BoxesCard::find($order->card_id)->price;
        $products=BoxesOrderProducts::select(  DB::raw('SUM(products.purchase_amount_excluding_tax) As total'))
        ->join('products', 'products.id', '=', 'boxes_order_products.product_id')->
        where('boxes_order_products.order_id',$id)->get();
        
        if($products->count()>0)
        return $prics+$products[0]->total;
        else
        return $prics;

    }
    public function products($id){
        $products=BoxesOrderProducts::select('products.*')
        ->join('products', 'products.id', '=', 'boxes_order_products.product_id')->where('boxes_order_products.order_id',$id)->get();
         return    ApiCartProductResource::Collection($products); 
    }
}
