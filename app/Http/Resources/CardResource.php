<?php

namespace App\Http\Resources;

use App\CardCurrency;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
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
            'name'=> $this->name,
            'image'=> $this->image,
            'currencies'=> $this->getCurrencyData()
        ];
    }


    /**
     * Get needed data
     *
     * @return void
     */
    protected function getCurrencyData()
    {
        $card_id = $this->id;
        return $this->currency->each(function($cur) use ($card_id){
            $cur->payment_mediums = $this->getPaymentMediums($cur->id, $card_id, $cur->pivot->buy_sell);
            $cur->buy_sell = $cur->pivot->buy_sell;
            $this->buy_sell = $cur->buy_sell;
            $this->unsetHelper($cur, ['pivot', 'created_at', 'updated_at']);
        });
    }


    /**
     * getPaymentMediums
     *
     * @param  mixed $currency_id
     * @param  mixed $card_id
     * @return void
     */
    protected  function getPaymentMediums($currency_id, $card_id, $buy_sell)
    {
        $cardCurrency = CardCurrency::where(['card_id'=> $card_id, 'currency_id'=> $currency_id, 'buy_sell' => $buy_sell ])->first();
        return $cardCurrency->paymentMediums()->where('payment_range_settings', '!=', '[]' )->get()->each(function($medium){
            $pricing = json_decode($medium->pivot->payment_range_settings);
            foreach ($pricing as $key => $value) {
                $percentage = $medium->pivot->percentage_deduction;
                $rate = $pricing[$key]->rate - (($percentage/100) * $pricing[$key]->rate);
                $pricing[$key]->rate = ceil($rate);
            }
            $medium->pricing = $pricing;
            $medium->percentage_deduction = $medium->pivot->percentage_deduction;
            $this->unsetHelper($medium, ['pivot', 'created_at', 'updated_at', 'currency_id']);
        });
    }



    /**
     * Unset keys we dont need
     *
     * @param  mixed $entity
     * @param  mixed $keys
     * @return void
     */
    private function unsetHelper(&$entity, array $keys)
    {
        foreach($keys as $key){

            unset($entity->$key);
        }
    }
}
