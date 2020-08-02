<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\SettingEmail as SettingEmailModel;
use App\Models\SettingApp as SettingAppModel;

use App\Providers\MailServiceProvider;

class Setting extends Controller
{
    
    public function add_setting_email(Request $request)
    {
        try {

            if(!check_access(['A_EDIT_EMAIL_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_email_setting_request($request);

            $request->type = 'SIMPLE';

            Artisan::call('config:clear');

            DB::beginTransaction();
            
            $email_setting = [
                "slack" => $this->generate_slack("setting_mail"),
                "type" => $request->type,
                "driver" => $request->driver,
                "host" => $request->host,
                "port" => $request->port,
                "username" => $request->username,
                "password" => $request->password,
                "encryption" => $request->encryption,
                "from_email" => $request->from_email,
                "from_email_name" => $request->from_email_name,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];

            $setting_id = SettingEmailModel::create($email_setting)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Email settings added successfully", 
                    "data"    => $email_setting['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function update_setting_email(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_EMAIL_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }
            
            $this->validate_email_setting_request($request);

            $email_setting_data_exists = SettingEmailModel::select('id')
            ->where([
                ['slack', '=', $slack]
            ])
            ->first();
            if (empty($email_setting_data_exists)) {
                throw new Exception("Trying to update invalid email setting", 400);
            }

            $request->type = 'SIMPLE';

            Artisan::call('config:clear');

            DB::beginTransaction();
            
            $email_setting = [
                "type" => $request->type,
                "driver" => $request->driver,
                "host" => $request->host,
                "port" => $request->port,
                "username" => $request->username,
                "password" => $request->password,
                "encryption" => $request->encryption,
                "from_email" => $request->from_email,
                "from_email_name" => $request->from_email_name,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = SettingEmailModel::where('slack', $slack)
            ->update($email_setting);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Email settings updated successfully", 
                    "data"    => $slack
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function update_setting_app(Request $request)
    {
        try {

            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_app_setting_request($request);

            Artisan::call('config:clear');

            DB::beginTransaction();

            $app_setting = SettingAppModel::select('*')->first();
            $file_name = $app_setting->company_logo;

            SettingAppModel::truncate();

            if($request->hasFile('company_logo')){

                $remove_file = $file_name;

                $upload_dir = Config::get('constants.upload.company.upload_path');
                $company_logo = $request->company_logo;

                $extension = $company_logo->getClientOriginalExtension();
                $file_name = 'logo_invoice_print_'.uniqid().'.'.$extension;
                $path = Storage::disk('company')->putFileAs('/', $company_logo, $file_name);
                $file_name = basename($path);

                $image = Image::make($company_logo);
                $file_path = $upload_dir.$file_name;
                $image->resize(160, 80);
                $image->save($file_path);
                $image->destroy();

                Storage::disk('company')->delete(
                    [
                        $remove_file
                    ]
                );
            }

            $app_setting = [
                "company_name" => $request->company_name,
                "app_date_time_format" => $request->date_time_format,
                "app_date_format" => $request->date_format,
                "company_logo" => $file_name,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = SettingAppModel::create($app_setting)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "App settings updated successfully",
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function remove_company_image(Request $request)
    {
        try {

            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $app_setting = SettingAppModel::select('company_name', 'company_logo')->first();

            if($app_setting->company_logo != ''){
                Storage::disk('company')->delete(
                    [
                        $app_setting->company_logo
                    ]
                );
            }

            $app_setting_array = [        
                'company_logo' => '',
            ];

            $data = SettingAppModel::where('company_name', $app_setting->company_name)->update($app_setting_array);
        
            return response()->json($this->generate_response(
                array(
                    "message" => "Company Logo removed successfully", 
                    "data"    => $data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function validate_email_setting_request($request)
    {
        $validator = Validator::make($request->all(), [
            'host' => $this->get_validation_rules("name_label", true),
            'port' => $this->get_validation_rules("name_label", true),
            'username' => $this->get_validation_rules("name_label", true),
            'password' => $this->get_validation_rules("name_label", true),
            'encryption' => $this->get_validation_rules("name_label", true),
            'from_email' => $this->get_validation_rules("email", true),
            'from_email_name' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    public function validate_app_setting_request($request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => $this->get_validation_rules("name_label", true),
            'date_time_format' => 'max:50|required',
            'date_format' => 'max:50|required',
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
