<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'store_id', 'supplier_code', 'name', 'email', 'phone', 'address', 'pincode', 'status', 'created_by', 'updated_by'];

}
