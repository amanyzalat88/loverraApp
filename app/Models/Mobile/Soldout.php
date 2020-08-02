<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ApiBoxesColorResource;

class Soldout extends Model {
     
    protected $table = 'souldout';
	protected $hidden = ['updated_at','created_at'];
    protected $fillable = [ 'id','product_id','customer_id','email'];

}
