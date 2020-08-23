<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Country extends Model
{
    protected $table="country";
    protected $fillable = ['name','name_ar','code','dial_code','currency_name','currency_code','currency_symbol','status','currency_rate_to_dinar'];
}
