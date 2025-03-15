<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class AuthController extends Controller
{
    public function register(Request $request)
    {
      // Validate dữ liệu đầu vào
    $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Tạo user mới
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Lấy role mặc định
        $role = Role::where('name', 'user')->first();
        if (!$role) {
            DB::rollBack();
            return response()->json([
                'message' => 'User role not found.'
            ], 500);
        }

        // Gán role cho user
        $user->roles()->attach($role);

        DB::commit();

        return response()->json([
            'message' => 'User created successfully!',
            'user'    => $user
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating user: ' . $e->getMessage());

        return response()->json([
            'message' => 'Failed to create user.'
        ], 500);
    }
    }

    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // If authentication is successful, create JWT
            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            $role = $user->roles()->pluck('name')->first();
            $customInfor = [
                'email' => $user->email,
                'name' => $user->name,
                'address' => $user->address,
                'role' => $role,
            ];
            // Return the token in the response
            return response()->json([
                'token' => $token,
                'user' => $customInfor,
                'message' => 'Login successful'
            ], 200);
        }

        // If authentication fails, return error message
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function me()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $roles = $user->roles()->pluck('name')->first();

        // Lấy role của user
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'roles' => $roles, // Lấy danh sách tên roles
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
