<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $hidden = ['id', 'store_id', 'discount_code_id', 'tax_code_id', 'created_by', 'updated_by'];
    protected $fillable = ['slack', 'name','shipping','free_shipping', 'store_code', 'tax_number', 'tax_code_id', 'discount_code_id', 'address', 'pincode', 'primary_contact', 'secondary_contact', 'primary_email', 'secondary_email', 'invoice_type', 'currency_code', 'currency_name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    
}
