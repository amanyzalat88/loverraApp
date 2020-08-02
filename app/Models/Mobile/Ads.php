<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Ads extends Model
{
    protected $table = 'ads';
    protected $hidden =  [];
    protected $fillable = ['id', 'title_ar', 'title_en','description_ar','description_en','photo','category_id'];
    public function getPhotoAttribute($value)
    {
        return url('public/'.$value);
    }
    public function getCategoryidAttribute($value)
    {
        return $this->hasSub($value)? 'subcategory/'.$value:'product/'.$value;
        
    }
    public  function  hasSub($id)
		{
			if (Category::where("parent" , $id)->count()>0)
			{
				return 1;
			}
			return 0;
		}
	
}
