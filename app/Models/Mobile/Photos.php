<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Photos extends Model
{
    protected $table = 'photos';
    protected $hidden =  ['created_at','updated_at'];
    protected $fillable = ['id', 'product_id', 'photo'];
    public function getPhotoAttribute($value)
    {
        return url('public/'.$value);
    }
}
