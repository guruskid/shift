<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NairaTradeResource extends JsonResource
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
            'id' => $this->id,
            'transactionType' =>"payBridge",
            'card' => 'PayBridge '.ucwords($this->type),
            'type' => $this->type,
            'amountNGN' =>$this->amount,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
