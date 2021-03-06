<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SettingApp extends Model
{
    protected $table = 'setting_app';
    protected $hidden = [];
    protected $fillable = ['company_name','email','phone', 'app_date_time_format', 'app_date_format', 'company_logo','address_ar','address_en','about_ar','about_en','twitter','insta', 'updated_by'];


    /* For view files */

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by') ->select(['slack', 'fullname', 'email', 'user_code']);;
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
