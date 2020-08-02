<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class BoxesColor extends Model {
     
    protected $table = 'boxes_color';
	protected $hidden = [ 'updated_at','created_at'];
    protected $fillable = [ 'id','name_ar','name_en','price','box_id','photo'];
    
	
   
}
