<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/naira/recieve-funds-dhfhshd',
        '/naira/recharge/dhfhd-q23-nfnd-dnf',
        '/naira/electricity/dddsfhd-q23-nfnd-dnf',
        '/admin/admin-hd-wallet-recieve-hghdhfh-ehe7sjdhsjaqwe',
        '/wallet-webhook'

    ];
}
