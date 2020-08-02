<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $hidden = ['id', 'order_id','slack', 'product_slack', 'status','created_at_label','status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
    protected $fillable = ['product_code', 'name', 'quantity', 'purchase_amount_excluding_tax', 'sale_amount_excluding_tax', 'sub_total_purchase_price_excluding_tax', 'sub_total_sale_price_excluding_tax', 'tax_code_id', 'tax_code', 'tax_percentage', 'tax_amount',   'discount_code_id', 'discount_code', 'discount_percentage', 'discount_amount', 'total_after_discount', 'total_amount'];

    public function scopeProduct($query){
        return $query->leftJoin('products', function ($join) {
            $join->on('products.id', '=', 'order_products.product_id');
        });
    }

    public function  Category($category_id,$lang){
        $label= $lang=='ar'? "label_ar": "label_en";
         return  Category::find($category_id)->$label;
    }
    public function scopeActive($query){
        return $query->where('order_products.status', 1);
    }

    /* For view files */
    
    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'ORDER_PRODUCT_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
