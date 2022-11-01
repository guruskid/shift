<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BitcoinTransaction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bitcoin_transactions')->insert([
            'user_id' => 4078,
            'primary_wallet_id' => 1,
            'wallet_id' => 'mzFYCuqhpCY8aYe91NMH6pufWv1bFCPpoG',
            'hash' => 'none',
            'credit' => 1.3,
            'debit' => null,
            'fee' => 0,
            'charge' => 0,
            'previous_balance' => 0,
            'current_balance' => 1.3,
            'transaction_type_id' => 19,
            'counterparty' => 'Dantown Asset',
            'narration' => 'Approved by 12',
            'confirmations' => 0,
            'status' => 'success',
        ]);

        DB::table('bitcoin_wallets')->insert([
            'user_id' => 4078,
            'path' => 'M/0H/0/6',
            'address' => 'mzFYCuqhpCY8aYe91NMH6pufWv1bFCPpoG',
            'type' => 'primary',
            'name' => 'Precious 19',
            'balance' => 2.3,
            'primary_wallet_id' => 1,

        ]);

    }
}
