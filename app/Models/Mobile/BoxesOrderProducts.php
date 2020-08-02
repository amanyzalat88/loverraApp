<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class BoxesOrderProducts extends Model {
     
    protected $table = 'boxes_order_products';
	protected $hidden = [ 'updated_at','created_at'];
    protected $fillable = [ 'id','product_id','order_id'];
    
	
   
}
