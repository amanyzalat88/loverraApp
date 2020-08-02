<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\ApiBoxesColorResource;

class Boxes extends Model {
     
    protected $table = 'boxes';
	protected $hidden = [ 'updated_at','created_at'];
    protected $fillable = [ 'id','name_ar','name_en','price','photo','count'];
    
	public function  Color($id){
        $color=BoxesColor::where('box_id',$id)->get() ;
         return ApiBoxesColorResource::collection($color); 
    }
    public function  hasColor($id){
        if (BoxesColor::where("box_id" , $id)->count()>0)
        {
            return 1;
        }
        return 0;
       
    }
}
