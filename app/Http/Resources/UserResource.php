<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
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
            'slack' => $this->slack,
            'user_code' => $this->user_code,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => new RoleResource($this->role),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_USER'], true))?route('user', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => $this->createdUser,
            'updated_by' => $this->updatedUser
        ];
    }
}
