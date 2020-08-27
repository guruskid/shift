<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
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
        return $this->hasMany('App\Transaction', 'user_email', 'email')->orderBy('created_at', 'desc' );
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
        return $this->hasMany('App\Notification')->orderBy('created_at', 'desc' );
    }

    public function assignedTransactions()
    {
        return $this->hasMany('App\Transaction', 'agent_id', 'id')->orderBy('created_at', 'desc' );
    }

    public function nairaWallet()
    {
        return $this->hasOne('App\NairaWallet');
    }


    public function notificationSetting()
    {
        return $this->hasOne('App\NotificationSetting');
    }
}
