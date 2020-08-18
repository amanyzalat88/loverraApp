<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BoxesResource extends Resource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'price' => $this->price,
            'count' => $this->count,
            'status' => new MasterStatusResource($this->status_data),
            
            'detail_link' => (check_access(['A_DETAIL_BOXES'], true))?route('boxes_detail', ['slack' => $this->slack]):'',
            'photo' => $this->photo,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
