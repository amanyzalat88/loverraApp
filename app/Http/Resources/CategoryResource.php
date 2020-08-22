<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CategoryResource extends Resource
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
            'parent' => $this->parent,
            'category_code' => $this->category_code,
            'photo' => $this->photo,
            'label_ar' => $this->label_ar,
            'label_en' => $this->label_en,
            'discount_code_id'=>$this->discount_code_id,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'discount_code' => new DiscountcodeResource($this->discount_code),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_CATEGORY'], true))?route('category', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
