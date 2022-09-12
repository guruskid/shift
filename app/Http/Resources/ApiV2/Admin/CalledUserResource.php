<?php

namespace App\Http\Resources\ApiV2\Admin;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Resources\Json\JsonResource;

class CalledUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $called_timeStamp = $this->called_date;
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->first_name." ".$this->user->last_name,
            'called_date' => Carbon::parse($called_timeStamp)->format('d M Y'),
            'called_time' => Carbon::parse($called_timeStamp)->format('h:ia'),
            'callDuration' => CarbonInterval::seconds($this->call_duration)->cascade()->forHumans(),
            'remark' => ($this->call_log) ? $this->call_log->call_response : null,
            'dp' => $this->user->dp
        ];
    }
}
