<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

use App\Http\Resources\MasterStatusResource;

class StoreResource extends Resource
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
            'store_code' => $this->store_code,
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'address' => $this->address,
            'pincode' => $this->pincode,
            'primary_contact' => $this->primary_contact,
            'secondary_contact' => $this->secondary_contact,
            'primary_email' => $this->primary_email,
            'secondary_email' => $this->secondary_email,
            'tax_code' => new TaxcodeResource($this->tax_code),
            'discount_code' => new DiscountcodeResource($this->discount_code),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_STORE'], true))?route('store', ['slack' => $this->slack]):'',
            'invoice_type' => $this->invoice_print_type,
            'currency_code' => $this->currency_code,
            'currency_name' => $this->currency_name,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
