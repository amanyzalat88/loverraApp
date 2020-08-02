<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Slider extends Model
{
    protected $table = 'slider';
    protected $hidden =  [];
    protected $fillable = ['id', 'photo_ar', 'photo_en'];
    public function getPhotoarAttribute($value)
    {
        return url('public/'.$value);
    }
    public function getPhotoenAttribute($value)
    {
        return url('public/'.$value);
    }
}
