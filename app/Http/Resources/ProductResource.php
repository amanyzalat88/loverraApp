<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
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
            'product_code' => $this->product_code,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'soldout' => $this->soldout,
            'photo' => $this->photo,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'quantity' => $this->quantity,
            'purchase_amount_excluding_tax' => $this->purchase_amount_excluding_tax,
            'sale_amount_excluding_tax' => $this->sale_amount_excluding_tax,
            'category' => new CategoryResource($this->category),
            'supplier' => new SupplierResource($this->supplier),
            'tax_code' => new TaxcodeResource($this->tax_code),
            'discount_code' => new DiscountcodeResource($this->discount_code),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_PRODUCT'], true))?route('product', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
