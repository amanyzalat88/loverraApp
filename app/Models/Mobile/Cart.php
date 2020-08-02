<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Cart extends Model
{
    protected $table = 'carts';
    protected $hidden =  [];
    protected $fillable = ['id', 'product_id', 'customer_id','quantity'];
    public function product($value)
   {
       return Product::find($value);
   }
     
}
