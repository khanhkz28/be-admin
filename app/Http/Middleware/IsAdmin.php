<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    
    public function handle(Request $request, Closure $next)
    {
          // Kiểm tra nếu người dùng đã đăng nhập và có vai trò 'admin'
          $user = Auth::user();
        
          // Kiểm tra nếu người dùng có vai trò 'admin' thông qua bảng trung gian
          $role = $user->roles()->where('name', 'admin')->first();
  
          if ($role) {
              return $next($request);
          }

        // Nếu không phải admin, trả về lỗi
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
