<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockfillOrder extends Model
{
    protected $guarded = [];

    /**
     * Get the currency that owns the BlockfillOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(CryptoCurrency::class, 'currency_id', 'id');
    }

    /**
     * Get the transaction that owns the BlockfillOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
