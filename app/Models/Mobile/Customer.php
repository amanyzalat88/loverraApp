<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable  {
     
    protected $table = 'customers';
    protected $hidden = ['id','created_at','updated_at','slack','created_by','updated_by'];
    protected $fillable = ['customer_type', 'name', 'email','password','api_token', 'phone', 'address', 'status'];

   public function getAuthIdentifier() {
		return $this->getKey();
	}
}
