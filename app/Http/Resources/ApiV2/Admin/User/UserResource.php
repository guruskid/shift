<?php

namespace App\Http\Resources\ApiV2\Admin\User;

use App\Http\Controllers\ApiV2\Admin\VerificationController;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $verificationLevel = $this->verificationHelper($this);
        $wallet = $this->nairaWallet;

        return [
            'id' => $this->id,
            'name' => $this->first_name." ".$this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'availableBalance' => ($wallet) ? $wallet->amount : 0,
            'LedgerBalance' => ($wallet) ? ( $wallet->amount + $wallet->withheld_amount) : 0,
            'Verification' => $verificationLevel,
        ];
    }

    public function verificationHelper($user)
    {
        $verificationLevel = 'not Verified';

        if($user->phone_verified_at != null AND $user->address_verified_at == null AND $user->idcard_verified_at == null)
        {
            $verificationLevel = 'Level 1';
        }
        if($user->phone_verified_at != null AND $user->address_verified_at != null AND $user->idcard_verified_at == null)
        {
            $verificationLevel = 'Level 2';
        }
        if($user->phone_verified_at != null AND $user->address_verified_at != null AND $user->idcard_verified_at != null)
        {
            $verificationLevel = 'Level 3';
        }

        return $verificationLevel;
    }

}
