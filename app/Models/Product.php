<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Product extends Model
{
    protected $table = 'products';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'product_code', 'name_ar', 'description_ar','name_en', 'description_en','soldout','photo', 'category_id', 'supplier_id', 'tax_code_id', 'discount_code_id', 'quantity', 'purchase_amount_excluding_tax', 'sale_amount_excluding_tax', 'status', 'created_by', 'updated_by'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }
    
    public function scopeActive($query){
        return $query->where('products.status', 1);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'products.status');
            $join->where('master_status.key', '=', 'PRODUCT_STATUS');
        });
    }

    public function scopeCategoryJoin($query){
        return $query->leftJoin('category', function ($join) {
            $join->on('category.id', '=', 'products.category_id');
        });
    }

    public function scopeSupplierJoin($query){
        return $query->leftJoin('suppliers', function ($join) {
            $join->on('suppliers.id', '=', 'products.supplier_id');
        });
    }

    public function scopeTaxcodeJoin($query){
        return $query->leftJoin('tax_codes', function ($join) {
            $join->on('tax_codes.id', '=', 'products.tax_code_id');
        });
    }

    public function scopeDiscountcodeJoin($query){
        return $query->leftJoin('discount_codes', function ($join) {
            $join->on('discount_codes.id', '=', 'products.discount_code_id');
        });
    }

    public function scopeCategoryActive($query){
        return $query->where('category.status', 1);
    }

    public function scopeSupplierActive($query){
        return $query->where('suppliers.status', 1);
    }

    public function scopeTaxcodeActive($query){
        return $query->where('tax_codes.status', 1);
    }

    public function scopeDiscountcodeActive($query){
        return $query->where('discount_codes.status', 1);
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'products.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'products.updated_by');
        });
    }

    public function scopeQuantityCheck($query, $quantity = ''){
        if($quantity != ""){
            return $query->where('products.quantity', '>=', $quantity);
        }else{
            return $query->where('products.quantity', '>', 0);
        }
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'PRODUCT_STATUS');
    }

    public function supplier(){
        return $this->hasOne('App\Models\Supplier', 'id', 'supplier_id');
    }

    public function category(){
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function tax_code(){
        return $this->hasOne('App\Models\Taxcode', 'id', 'tax_code_id');
    }

    public function discount_code(){
        return $this->hasOne('App\Models\Discountcode', 'id', 'discount_code_id');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
