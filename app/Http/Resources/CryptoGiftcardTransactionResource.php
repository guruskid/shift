<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CryptoGiftcardTransactionResource extends JsonResource
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
            'transactionType' =>"CryptoGiftCard",
            'card' => $this->card,
            'card_price' => $this->card_price,
            'card_type' => $this->card_type,
            'quantity' => $this->quantity,
            'country' =>$this->country,
            'type' => $this->type,
            'amountUSD' =>$this->amount,
            'amountNGN' =>(int)$this->amount_paid,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
