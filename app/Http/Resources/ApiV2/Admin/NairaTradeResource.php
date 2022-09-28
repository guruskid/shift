<?php

namespace App\Http\Resources\ApiV2\Admin;

use App\Account;
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
        $accountDetails = null;
        if($this->type == 'withdrawal')
        {
            $account = $this->account;
            $accountDetails = $account->account_name." ".$account->bank_name." ".$account->account_number;
        }

        $prev_bal = $this->naira_transactions->previous_balance;
        $current_balance = $this->naira_transactions->current_balance;
        $charge = $this->naira_transactions->charge;

        $user = $this->user;
        $name = $user->first_name." ".$user->last_name;
        $username = $user->username;
        $phone = $user->phone;

        $accountant = $this->agent;
        $accountant_name = $accountant->first_name." ".$accountant->last_name;

        return [
            'id' => $this->id,
            'name' => $name,
            'username' => $username,
            'phone' => $phone,
            'charges' => $charge,
            'TransactionType' => $this->type,
            'accountDetails' => $accountDetails,
            'previous_balance' => $prev_bal,
            'current_balance' => $current_balance,
            'status' => $this->status,
            'Accountant' => $accountant_name,
            'Date' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
