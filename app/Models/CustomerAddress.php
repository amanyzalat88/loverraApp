<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'customers_addresses';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'delivery_area', 'building', 'street', 'flatnumber', 'landmark', 'country', 'city' ,'customer_id', 'created_at', 'updated_at'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeSkipDefaultCustomer($query){
        return $query->where('customer_type', '!=', 'DEFAULT');
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'customers.status');
            $join->where('master_status.key', '=', 'CUSTOMER_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'customers.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'customers.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'SUPPLIER_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
