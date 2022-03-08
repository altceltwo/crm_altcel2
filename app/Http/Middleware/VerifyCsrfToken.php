<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/notifications-webhook',
        '/overdue-payments',
        '/create-payments',
        '/create-payments-ethernet',
        '/check-payments-overdue',
        '/create-payments-sandbox',
        '/change-product',
	    '/updateCoordinate',
        '/get-offers-rates-surplus',
        '/create-reference-api',
        'get-rates-alta-api',
        '/activationsGeneralApi',
        '/rollback-preactivate-api/*',
        '/activate-deactivate/DN-api',
        '/purchase-api',
        'consultaCortes',
        'updateStatusCortes',
        'payAll',
        '/webhook-altan-redes',
        '/extract-csv',
        '/consume-csv',
        '/charge-csv-inventory',
    ];
}
