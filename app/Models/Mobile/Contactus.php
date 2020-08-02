<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class Contactus extends Model {
     
    protected $table = 'contact_us';
	protected $hidden = [ 'updated_at','created_at'];
    protected $fillable = [ 'id','name','phone', 'email', 'message','customer_id'];
    
	
   
}
