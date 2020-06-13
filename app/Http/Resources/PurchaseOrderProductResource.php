<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PurchaseOrderProductResource extends Resource
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
            'product_slack' => $this->product_slack,
            'product_code' => $this->product_code,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'amount_excluding_tax' => $this->amount_excluding_tax,
            'subtotal_amount_excluding_tax' => $this->subtotal_amount_excluding_tax,
            'discount_percentage' => $this->discount_percentage,
            'tax_percentage' => $this->tax_percentage,
            'discount_amount' => $this->discount_amount,
            'total_after_discount' => $this->total_after_discount,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
            'status' => new MasterStatusResource($this->status_data),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}