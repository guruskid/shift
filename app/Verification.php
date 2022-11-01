<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    protected $appends = ['level'];
    protected $guarded = [];

    /**
     * Get the user that owns the Verification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function verifiedUserBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    public function getLevelAttribute($level) {
        if ($this->type === 'Address') {
            return 2;
        }
        if ($this->type === 'ID Card') {
            return 3;
        }
    }
}
