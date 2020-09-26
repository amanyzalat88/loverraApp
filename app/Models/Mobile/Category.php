<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
 

class Category extends Model {
     
    protected $table = 'category';
	 protected $hidden = [ 'store_id','slack','store_id','created_by','updated_by','status','updated_at','created_at'];
    protected $fillable = [ 'id','category_code','parent', 'label_en', 'description_en','label_ar', 'description_ar','photo'];
    
	public  function  hasSub($id)
		{
			if (Category::find($id)->where("parent" , $id)->count()>0)
			{
				return 1;
			}
			return 0;
		}
	
	 public function SubCategories()
		{
			return $this->hasMany(Category::class);
		}
  
     public static function MainCategories()
		{
			return Category::where('parent',0)->where('status',1)->get();
		}
		public function parseDate($date){
			return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
		}
}
