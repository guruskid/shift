<?php

namespace App\Http\Resources\ApiV2\Admin;

use App\Http\Controllers\ApiV2\Admin\VerificationController;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplianceFraudResource extends JsonResource
{
    private static $start;
    private static $end;
    private static $usd_rate;
    private static $type;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = self::$type;
        $userDebitCreditDetail = $this->getUserTransactionVerificationData($this);

        $verification = new VerificationController();
        $verificationLevel = $verification->verificationHelper($this);
        $verificationMonthlyData = $verification->maximumLevelMonthlyWithdrawal($verificationLevel);

        $debitAmount = ($type == "NGN") ? $userDebitCreditDetail['debitAmountNGN'] : $userDebitCreditDetail['debitAmountUSD'];
        $creditAmount = ($type == "NGN") ? $userDebitCreditDetail['creditAmountNGN'] : $userDebitCreditDetail['creditAmountUSD'];

        return [
            'id' => $this->id,
            'signupDate' => $this->created_at->format('d/m/Y'),
            'maximumWithdrawal' => $verificationMonthlyData,
            'DebitCount' => $userDebitCreditDetail['debitDataCount'],
            'DebitAmount' => $debitAmount,
            'CreditCount' => $userDebitCreditDetail['creditDataCount'],
            'CreditAmount' => $creditAmount,
            'Verification' => $verificationLevel,
        ];
    }

    public static function sortCollection($collection, $start, $end, $usd_rate, $type) : AnonymousResourceCollection
    {
        self::$start = $start;
        self::$end = $end;
        self::$usd_rate = $usd_rate;
        self::$type = $type;
        return parent::collection($collection);
    }

    public function getUserTransactionVerificationData($user)
    {
        $start = self::$start;
        $end = self::$end;

        $transactions = $this->transactions->where('status','success');
        $utility = $this->utilityTransaction->where('status','success');
        $payBridge = $this->nairaTrades->where('status','success');

        $tranxData = $transactions->where('created_at','>=',$start)->where('created_at','<=',$end);
        $utilData = $utility->where('created_at','>=',$start)->where('created_at','<=',$end);
        $pbTranx = $payBridge->where('created_at','>=',$start)->where('created_at','<=',$end);

        $debitData = $this->DebitData($tranxData, $utilData ,$pbTranx);
        $debitDataCount = $debitData['pbTranxCount'] + $debitData['utilDataCount'] + $debitData['tranxDataCount'];
        $debitAmountNGN = $debitData['pbTranxAmountNGN'] + $debitData['utilDataAmountNGN'] + $debitData['tranxDataAmountNGN'];
        $debitAmountUSD = $debitData['pbTranxAmountUSD'] + $debitData['utilDataAmountUSD'] + $debitData['tranxDataAmountUSD'];

        $creditData = $this->creditData($tranxData ,$pbTranx);
        $creditDataCount = $creditData['pbTranxCount'] + $creditData['tranxDataCount'];
        $creditAmountNGN = $creditData['pbTranxAmountNGN'] + $creditData['tranxDataAmountNGN'];
        $creditAmountUSD = $creditData['pbTranxAmountUSD'] + $creditData['tranxDataAmountUSD'];

        $exportData = array(
            'debitDataCount' => $debitDataCount,
            'debitAmountNGN' => $debitAmountNGN,
            'debitAmountUSD' => $debitAmountUSD,
            'creditDataCount' => $creditDataCount,
            'creditAmountNGN' => $creditAmountNGN,
            'creditAmountUSD' => $creditAmountUSD,
        );

        return $exportData;
    }

    public function DebitData($transactions, $Utility, $payBridge)
    {
        //paybridge
        $payBridgeDebit = $payBridge->where('type','withdrawal');
        $pbData = $this->utilityPayBridgeSummary($payBridgeDebit);

        $pbTranxCount = $pbData['count']; 
        $pbTranxAmountNGN = $pbData['amountNGN'];
        $pbTranxAmountUSD = $pbData['amountUSD'];

        //Utility
        $utilData = $this->utilityPayBridgeSummary($Utility);

        $utilDataCount = $utilData['count']; 
        $utilDataAmountNGN = $utilData['amountNGN']; 
        $utilDataAmountUSD = $utilData['amountUSD']; 

        //Crypto
        $CryptoDeposit = $transactions->where('type','buy');

        $cryptoData = $this->transactionSummary($CryptoDeposit);
        $tranxDataCount = $cryptoData['count']; 
        $tranxDataAmountNGN = $cryptoData['amountNGN']; 
        $tranxDataAmountUSD = $cryptoData['amountUSD']; 

        $exportData = array(
            'pbTranxCount' => $pbTranxCount,
            'pbTranxAmountNGN' => $pbTranxAmountNGN,
            'pbTranxAmountUSD' => $pbTranxAmountUSD,
            'utilDataCount' => $utilDataCount,
            'utilDataAmountNGN' => $utilDataAmountNGN,
            'utilDataAmountUSD' => $utilDataAmountUSD,
            'tranxDataCount' => $tranxDataCount,
            'tranxDataAmountNGN' => $tranxDataAmountNGN,
            'tranxDataAmountUSD' => $tranxDataAmountUSD,
        );

        return $exportData;
    }

    public function creditData($transactions, $payBridge)
    {
        $payBridgeDebit = $payBridge->where('type','deposit');
        $pbData = $this->utilityPayBridgeSummary($payBridgeDebit);
        $pbTranxCount = $pbData['count']; 
        $pbTranxAmountNGN = $pbData['amountNGN'];
        $pbTranxAmountUSD = $pbData['amountUSD'];


        $CryptoDeposit = $transactions->where('type','sell');
        $cryptoData = $this->transactionSummary($CryptoDeposit);
        $tranxDataCount = $cryptoData['count']; 
        $tranxDataAmountNGN = $cryptoData['amountNGN']; 
        $tranxDataAmountUSD = $cryptoData['amountUSD']; 

        $exportData = array(
            'pbTranxCount' => $pbTranxCount,
            'pbTranxAmountNGN' => $pbTranxAmountNGN,
            'pbTranxAmountUSD' => $pbTranxAmountUSD,
            'tranxDataCount' => $tranxDataCount,
            'tranxDataAmountNGN' => $tranxDataAmountNGN,
            'tranxDataAmountUSD' => $tranxDataAmountUSD,
        );

        return $exportData;
    }

    public function transactionSummary($transaction)
    {
        $tranxDataCount = $transaction->count();
        $tranxDataAmountNGN = $transaction->sum('amount_paid');
        $tranxDataAmountUSD = $transaction->sum('amount');

        $exportData = array(
            'count' => $tranxDataCount,
            'amountNGN' => $tranxDataAmountNGN,
            'amountUSD' => $tranxDataAmountUSD,
        );

        return $exportData;
    }

    public function utilityPayBridgeSummary($transaction)
    {
        $usd_rate = self::$usd_rate;

        $dataCount = $transaction->count();
        $dataAmountNGN = $transaction->sum('amount');
        $dataAmountUSD = $dataAmountNGN / $usd_rate ;

        $exportData = array(
            'count' => $dataCount,
            'amountNGN' => $dataAmountNGN,
            'amountUSD' => $dataAmountUSD,
        );

        return $exportData;
    }
}
