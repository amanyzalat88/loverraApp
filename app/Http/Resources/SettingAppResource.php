<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SettingAppResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'company_name' => $this->company_name,
            'address_ar' => $this->address_ar,
            'address_en' => $this->address_en,
            'about_ar' => $this->about_ar,
            'about_en' => $this->about_en,
            'twitter' => $this->twitter,
            'insta' => $this->insta,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'app_date_time_format' => $this->app_date_time_format,
            'app_date_format' => $this->app_date_format,
            'company_logo' => $this->company_logo,
            'company_logo_path' => ($this->company_logo == '')?config('constants.upload.company.default'):config('constants.upload.company.view_path').$this->company_logo,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
