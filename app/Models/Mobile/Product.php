<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Product extends Model
{
    protected $table = 'products';
    protected $hidden = ['id', 'store_id','slack', 'store_id','purchase_amount_excluding_tax', 'sale_amount_excluding_tax', 'status', 'created_by', 'updated_by','created_at','updated_at' ];
    protected $fillable = ['product_code', 'name', 'description', 'category_id', 'supplier_id', 'tax_code_id', 'discount_code_id', 'quantity','photo','soldout','available' ];

   
  

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function photos($id){
        return Photos::where('product_id',$id)->get();
    }

    public function supplier($supplier_id){
		 if($sup=Supplier::find($supplier_id))
        return $sup->name;
	    
    }

    public function  Category($category_id,$lang){
        $label= $lang=='ar'? "label_ar": "label_en";
		if($cat=Category::find($category_id))
         return  $cat->$label;
    }

   
    public function tax_code(){
        return $this->hasOne('App\Models\Taxcode', 'id', 'tax_code_id');
    }

    public function discount_code(){
        return $this->hasOne('App\Models\Discountcode', 'id', 'discount_code_id');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
