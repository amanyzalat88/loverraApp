<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class Setting extends Model {
     
    protected $table = 'setting_app';
	protected $hidden = [ 'updated_at','created_at'];
    protected $fillable = [ 'id','address','phone', 'email', 'about','twitter','insta'];
    
	
   
}
