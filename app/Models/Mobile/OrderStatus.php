<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class OrderStatus extends Model {
     
    protected $table = 'order_status';
	protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['id','name_ar', 'name_en'];
    
 
   
}
