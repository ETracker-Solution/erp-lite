<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ROMOpeningBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid ?? '',
            'date' => $this->date,
            'group' => $this->chartOfInventory->parent->name,
            'group_id' => $this->chartOfInventory->parent->id,
            'item_name' => $this->chartOfInventory->name,
            'item_id' => $this->chartOfInventory->id,
            'unit' => $this->chartOfInventory->unit ? $this->chartOfInventory->unit->name : '',
            'qty' => $this->quantity,
            'rate' => $this->rate,
            'value' => $this->amount,
            'store' => $this->pHouse->name,
            'store_id' => $this->store_id,
            'remarks' => $this->remarks,
        ];
    }
}
