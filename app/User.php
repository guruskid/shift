<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [ 'role' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function transactions()
    {
        return $this->hasMany('App\Transaction')->latest();
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function accounts()
    {
        return $this->hasMany('App\Account');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification')->latest();
    }

    public function assignedTransactions()
    {
        return $this->hasMany('App\Transaction', 'agent_id', 'id')->latest();
    }

    public function approvedTransactions()
    {
        return $this->hasMany('App\Transaction', 'accountant_id', 'id')->latest();
    }

    public function nairaWallet()
    {
        return $this->hasOne('App\NairaWallet');
    }

    public function nairaTransactions()
    {
        return $this->hasMany('App\NairaTransaction');
    }

    public function notificationSetting()
    {
        return $this->hasOne('App\NotificationSetting');
    }

    public function bitcoinWallet()
    {
        return $this->hasOne('App\BitcoinWallet');
    }

    public function verifications()
    {
        return $this->hasMany('App\Verification');
    }


    public function nairaTrades()
    {
        return $this->hasMany(NairaTrade::class)->latest();
    }
}
