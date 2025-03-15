<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->middleware('isAdmin');
        $this->userRepository = $userRepository;
    }

    // Get all users
    public function index(Request $request)
    {
        try {
            $status = $request->query('status');
            $role = $request->query('role');
    
            $users = $this->userRepository->getAllUsers($status, $role);
    
            return response()->json($users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'status' => $user->status,
                    'roles' => $user->roles()->pluck('name')->first(), // Lấy role của từng user
                ];
            }));
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    // Get single user
    public function show($id)
    {
        try {
            $user = $this->userRepository->getUserById($id);
            $roles = $user->roles()->pluck('name')->first();
            return response()->json(['user' => $user, 'roles' => $roles]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    // Create a new user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string|in:admin,user',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $user = $this->userRepository->createUser($request->all());
            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $user = $this->userRepository->updateUser($id, $request->all());
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateByEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email', // Xóa dấu phẩy
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            
            return response()->json($validator->errors(), 400);
        }

        try {

            $user = $this->userRepository->updateUserByEmail($request->all());
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }
    // Update role
    public function updateRole(Request $request, $id)
    {
        try {
            $roleName = $request->role ?: 'user';
            $user = $this->userRepository->updateUserRole($id, $roleName);

            if ($user) {
                return response()->json(['message' => 'User role updated successfully', 'user' => $user]);
            } else {
                return response()->json(['error' => 'Role not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete user
    public function destroy($id)
    {
        try {
            $this->userRepository->deleteUser($id);
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }
}
