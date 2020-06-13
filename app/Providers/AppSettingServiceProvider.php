<?php

namespace App\Providers;

use Config;
use Illuminate\Support\ServiceProvider;
use App\Models\SettingApp as SettingAppModel;

class AppSettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Schema::hasTable('setting_app')) {
            $app_setting = SettingAppModel::select('*')->first();
            if ($app_setting) //checking if table is not empty
            {    
                config([
                    'app.company' => $app_setting->company_name,
                    'app.date_time_format' => $app_setting->app_date_time_format,
                    'app.date_format' => $app_setting->app_date_format,
                    'app.company_logo' => $app_setting->company_logo,
                ]);
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
