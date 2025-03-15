<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        // Xử lý lỗi JWT
        if ($exception instanceof JWTException) {
            return response()->json([
                'message' => 'Token is invalid or expired'
            ], 401); // Trả về mã lỗi 401 nếu token không hợp lệ
        }

        // Xử lý lỗi khi người dùng chưa đăng nhập
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated, please login again'
            ], 401); // Trả về mã lỗi 401 nếu chưa xác thực
        }

        // Các lỗi khác sẽ được xử lý mặc định
        return parent::render($request, $exception);
    }
}
