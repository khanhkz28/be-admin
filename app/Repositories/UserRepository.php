<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    // Get all users with optional filters (status, role)
    public function getAllUsers($status = null, $role = null)
    {
        return User::when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
            ->when($role, function ($query, $role) {
                return $query->whereHas('roles', function ($query) use ($role) {
                    $query->where('name', $role);
                });
            })
            ->get();
    }

    // Get single user by ID
    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    // Create a new user
    public function createUser($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (isset($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $user->roles()->attach($role);
            }
        }

        return $user;
    }

    // Update an existing user
    public function updateUser($id, $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }
    public function updateUserByEmail($data)
    {
        $user = User::where('email', $data['email'])->firstOrFail();
        $user->update($data);
        return $user;
    }

    // Delete a user by ID
    public function deleteUser($id)
    {
        $authUserId = auth()->id(); // Lấy ID của user hiện tại

        if ($authUserId == $id) {
            return response()->json(['message' => 'Bạn không thể tự xóa chính mình!'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Xóa user thành công!']);
    }

    // Update user role
    public function updateUserRole($id, $roleName)
    {
        $user = User::findOrFail($id);
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
            return $user;
        }

        return null;
    }
}
