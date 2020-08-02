<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Photos extends Model
{
    protected $table = 'photos';
    protected $hidden =  [];
    protected $fillable = ['id', 'product_id', 'photo'];

}
