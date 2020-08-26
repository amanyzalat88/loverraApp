<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class Order extends Model {
     
    protected $table = 'orders';
	protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at'];
    protected $fillable = ['id','slack', 'store_id','customer_address', 'order_number', 'customer_id', 'customer_phone', 'customer_email', 'purchase_amount_subtotal_excluding_tax', 'sale_amount_subtotal_excluding_tax', 'store_level_tax_code_id', 'store_level_tax_code', 'store_level_total_tax_percentage', 'store_level_total_tax_amount', 'store_level_total_tax_components', 'product_level_total_tax_amount', 'total_tax_amount', 'store_level_discount_code_id', 'store_level_discount_code', 'store_level_total_discount_percentage', 'store_level_total_discount_amount', 'product_level_total_discount_amount', 'total_discount_amount', 'total_after_discount', 'total_order_amount', 'payment_method_id', 'payment_method_slack', 'payment_method', 'status','order_status_id'];
   
    public function  Status($order_status_id,$lang){
        $name= $lang=='ar'? "name_ar": "name_en";
         return  OrderStatus::find($order_status_id)->$name;
    }
    
    public function products(){
        return $this->hasMany('App\Models\Mobile\OrderProduct', 'order_id', 'id');
    }
    
   
}
