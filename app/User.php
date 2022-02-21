<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{

    public static function getUsers(){
        $datas = DB::table('users')->select('first_name', 'last_name', 'email', 'phone', 'created_at')->orderBy('id', 'asc')->get()->toArray();
    }

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

    public function utilityTransaction()
    {
        return $this->hasMany('App\UtilityTransaction')->latest();
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



    public function btcWallet(): HasOne
    {
        return $this->hasOne(Wallet::class)->where('currency_id', 1);
    }

    public function ethWallet(): HasOne
    {
        return $this->hasOne(Wallet::class)->where('currency_id', 2);
    }

    public function tronWallet(): HasOne
    {
        return $this->hasOne(Wallet::class)->where('currency_id', 5);
    }

    public function usdtWallet(): HasOne
    {
        return $this->hasOne(Wallet::class)->where('currency_id', 7);
    }


    public function nairaTrades()
    {
        return $this->hasMany(NairaTrade::class)->latest();
    }

    public function agentNairaTrades(): HasMany
    {
        return $this->hasMany(NairaTrade::class, 'agent_id', 'id')->latest();
    }

    public function agentLimits(): HasOne
    {
        return $this->hasOne(AgentLimit::class);

    }
}
