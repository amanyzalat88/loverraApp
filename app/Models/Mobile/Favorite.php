<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Favorite extends Model
{
    protected $table = 'favorite';
    protected $hidden =  ['created_at','updated_at'];
    protected $fillable = ['id', 'product_id', 'customer_id'];
    
   
    public function product(){
        return $this->hasOne('App\Models\Mobile\Product', 'id', 'product_id');
    }
}
