<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Order extends Model
{
    protected $table = 'orders';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'store_id', 'order_number', 'customer_id', 'customer_phone', 'customer_email', 'purchase_amount_subtotal_excluding_tax', 'sale_amount_subtotal_excluding_tax', 'store_level_tax_code_id', 'store_level_tax_code', 'store_level_total_tax_percentage', 'store_level_total_tax_amount', 'store_level_total_tax_components', 'product_level_total_tax_amount', 'total_tax_amount', 'store_level_discount_code_id', 'store_level_discount_code', 'store_level_total_discount_percentage', 'store_level_total_discount_amount', 'product_level_total_discount_amount', 'total_discount_amount', 'total_after_discount', 'total_order_amount', 'payment_method_id', 'payment_method_slack', 'payment_method', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeClosed($query){
        return $query->where('orders.status', 1);
    }
    
    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'orders.status');
            $join->where('master_status.key', '=', 'ORDER_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'orders.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'orders.updated_by');
        });
    }

   /* For view files */

    public function products(){
        return $this->hasMany('App\Models\OrderProduct', 'order_id', 'id')->where('order_products.status', 1);
    }

    public function storeData(){
        return $this->hasOne('App\Models\Store', 'id', 'store_id');
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'ORDER_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
