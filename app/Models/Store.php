<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $hidden = ['id', 'store_id', 'discount_code_id', 'tax_code_id', 'created_by', 'updated_by'];
    protected $fillable = ['slack', 'name', 'store_code', 'tax_number', 'tax_code_id', 'discount_code_id', 'address', 'pincode', 'primary_contact', 'secondary_contact', 'primary_email', 'secondary_email', 'invoice_type', 'currency_code', 'currency_name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'stores.status');
            $join->where('master_status.key', '=', 'STORE_STATUS');
        });
    }

    public function scopeTaxcodeJoin($query){
        return $query->leftJoin('tax_codes', function ($join) {
            $join->on('tax_codes.id', '=', 'stores.tax_code_id');
        });
    }

    public function scopeDiscountcodeJoin($query){
        return $query->leftJoin('discount_codes', function ($join) {
            $join->on('discount_codes.id', '=', 'stores.discount_code_id');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'stores.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'stores.updated_by');
        });
    }

    /* For view files */

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'STORE_STATUS');
    }

    public function tax_code(){
        return $this->hasOne('App\Models\Taxcode', 'id', 'tax_code_id')->where('status', 1);
    }

    public function discount_code(){
        return $this->hasOne('App\Models\Discountcode', 'id', 'discount_code_id')->where('status', 1);
    }

    public function invoice_print_type(){
        return $this->hasOne('App\Models\MasterInvoicePrintType', 'print_type_value', 'invoice_type')->where('status', 1);
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
