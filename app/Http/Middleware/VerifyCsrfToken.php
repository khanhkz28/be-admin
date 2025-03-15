<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*', // Bỏ qua CSRF cho tất cả các endpoint API
        // 'payment/webhook', // Bỏ qua CSRF cho webhook thanh toán
    ];
}
