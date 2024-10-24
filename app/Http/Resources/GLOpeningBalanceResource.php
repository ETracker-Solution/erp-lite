<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GLOpeningBalanceResource extends JsonResource
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
            'item_name' => $this->chartOfAccount->name,
            'item_id' => $this->chartOfAccount->id,
            'amount' => $this->amount,
            'remarks' => $this->remarks,
        ];
    }
}
