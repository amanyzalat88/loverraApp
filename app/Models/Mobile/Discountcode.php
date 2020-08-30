<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Discountcode extends Model
{
    protected $table = 'discount_codes';
    protected $hidden = ['slack', 'store_id','created_by', 'updated_by', 'created_at', 'updated_at'];
    protected $fillable = ['id', 'store_id', 'label', 'discount_code', 'discount_percentage', 'description', 'status','discount_type','discount_num','discount_from','discount_to'];

   
}
