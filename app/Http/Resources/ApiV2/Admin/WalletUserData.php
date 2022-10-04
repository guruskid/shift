<?php

namespace App\Http\Resources\ApiV2\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletUserData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->user;
        return [
            'id' => $this->id,
            'name' =>  ($user) ? $user->first_name." ".$user->last_name : null ,
            'email' => ($user) ?$user->email : null,
            'NairaBalance' => $this->amount,
            'signupBalance' => ($user) ? $user->created_at->format('d/m/Y H:i:s a') : null,
        ];
    }
}
