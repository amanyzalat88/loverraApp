<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Category extends Model
{
    protected $table = 'category';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack','parent','discount_code_id', 'store_id', 'category_code', 'label_ar', 'description_ar','label_en', 'description_en','photo', 'status', 'created_by', 'updated_by'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function discount_code(){
        return $this->hasOne('App\Models\Discountcode', 'id', 'discount_code_id');
    }

    public function scopeDiscountcodeJoin($query){
        return $query->leftJoin('discount_codes', function ($join) {
            $join->on('discount_codes.id', '=', 'category.discount_code_id');
        });
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }
    public function subcategory(){
        return $this->hasMany('App\Models\Category', 'parent');
    }

    public function scopeSortLabelAsc($query){
        return $query->orderBy('category.label_en', 'asc');
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'category.status');
            $join->where('master_status.key', '=', 'CATEGORY_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'category.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'category.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'CATEGORY_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
