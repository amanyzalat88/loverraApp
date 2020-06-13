<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\SettingEmail as SettingEmailModel;
use App\Models\SettingApp as SettingAppModel;
use App\Models\MasterStatus;
use App\Models\MasterDateFormat;

use App\Http\Resources\SettingEmailResource;
use App\Http\Resources\SettingAppResource;

class Setting extends Controller
{
    public function email_setting(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_EMAIL_SETTING';
        check_access([$data['sub_menu_key']]);
    
        $email_setting = SettingEmailModel::select('*')->first();
        $email_setting_data = collect();
        if(!empty($email_setting)){
            $email_setting_data = new SettingEmailResource($email_setting);
        }
        $data['email_setting'] = $email_setting_data;

        return view('setting.email.email_setting', $data);
    }

    public function edit_email_setting($slack = null){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_EMAIL_SETTING';
        $data['action_key'] = 'A_EDIT_EMAIL_SETTING';
        check_access([$data['action_key']]);

        $email_setting = SettingEmailModel::select('*')
        
        ->when($slack, function ($query, $slack) {
            $query->where('slack', $slack);
        })

        ->first();
        
        $email_setting_data = collect();
        if(!empty($email_setting)){
            $email_setting_data = new SettingEmailResource($email_setting);
        }
        $data['setting_data'] = $email_setting_data;

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('MAIL_SETTING_STATUS')->active()->sortValueAsc()->get();

        return view('setting.email.edit_email_setting', $data);
    }

    public function app_setting(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_APP_SETTING';
        check_access([$data['sub_menu_key']]);
    
        $app_setting = SettingAppModel::select('*')->first();
        $app_setting_data = collect();
        if(!empty($app_setting)){
            $app_setting_data = new SettingAppResource($app_setting);
        }
        $data['setting_data'] = $app_setting_data;

        return view('setting.app.app_setting', $data);
    }

    public function edit_app_setting($slack = null){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_APP_SETTING';
        $data['action_key'] = 'A_EDIT_APP_SETTING';
        check_access([$data['action_key']]);

        $data['date_time_formats'] = MasterDateFormat::select('date_format_value', 'date_format_label')->where([
            ['key', '=', 'DATE_TIME_FORMAT'],
            ['status', '=', 1],
        ])->get();

        $data['date_formats'] = MasterDateFormat::select('date_format_value', 'date_format_label')->where([
            ['key', '=', 'DATE_FORMAT'],
            ['status', '=', 1],
        ])->get();

        $app_setting = SettingAppModel::select('*')
        ->first();
        
        $app_setting_data = collect();
        if(!empty($app_setting)){
            $app_setting_data = new SettingAppResource($app_setting);
        }
        $data['setting_data'] = $app_setting_data;

        return view('setting.app.edit_app_setting', $data);
    }
}
