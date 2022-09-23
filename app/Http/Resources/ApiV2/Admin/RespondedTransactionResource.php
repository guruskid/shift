<?php

namespace App\Http\Resources\ApiV2\Admin;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class RespondedTransactionResource extends JsonResource
{
    private static $data;
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
            'user_id' => $this->user_id,
            'name' => $this->user->first_name." ".$this->user->last_name,
            'email' => $this->user->email,
            'assetType' => ($this->tranxCard) ? $this->tranxCard : $this->card,
            'assetValue' => (in_array($this->card_id,self::$data)) ? ($this->quantity * $this->amount) : $this->amount,
            'date' =>$this->updated_at->format('d M y, h:ia'),
            'agent' => ($this->agent) ? $this->agent->first_name." ".$this->agent->last_name : 'none' 
        ];
    }

    public static function customCollection($collection, $data) : AnonymousResourceCollection
    {
        self::$data = $data;
        return parent::collection($collection);
    }
}
