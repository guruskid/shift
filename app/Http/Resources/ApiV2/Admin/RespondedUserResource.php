<?php

namespace App\Http\Resources\ApiV2\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RespondedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $allTranx = collect()->concat($this->transactions)->concat($this->depositTransactions)->concat($this->utilityTransaction);
        $data = $allTranx->where('created_at','>=',$this->called_date)->sortByDesc('created_at')->first();

        $lastTranxDate = $data->created_at;
        $lastTranxVolume = $data->amount;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->first_name." ".$this->user->last_name,
            'username' => $this->user->username,
            'signUpDate' => $this->user->created_at->format('d M Y'),
            'Responded_Cycle' => $this->Responded_Cycle ?: 0,
            'Recalcitrant_Cycle' => $this->Recalcitrant_Cycle ?: 0,
            'lastTranxDate' => $lastTranxDate,
            'lastTranxVolume' => $lastTranxVolume,
            'dp' => $this->user->dp
        ];
    }
}
