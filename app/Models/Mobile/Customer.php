<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable  {
     
    protected $table = 'customers';
    protected $hidden = ['customer_type','id','address', 'status','created_at','updated_at','slack','created_by','updated_by'];
    protected $fillable = ['name', 'email','password','phone','gender','api_token','country'];

   public function getAuthIdentifier() {
		return $this->getKey();
	}
}
