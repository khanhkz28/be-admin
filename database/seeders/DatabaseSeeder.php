<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Gọi các seeder trong đây
        
        // 1. Seeder thêm quyền vào bảng permissions
        $permissions = [
            'view_posts',
            'create_posts',
            'edit_posts',
            'delete_posts',
            'manage_users',
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            // Kiểm tra xem quyền đã tồn tại chưa
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
        // 2. Seeder thêm các vai trò vào bảng roles
        $roles = [
            'admin',
            'user',
            'editor',
        ];

        foreach ($roles as $role) {
            // Kiểm tra nếu role chưa tồn tại trong bảng roles
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }

        // 3. Seeder gán quyền cho vai trò 'admin'
        $adminRole = Role::where('name', 'admin')->first();
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $adminRole->permissions()->attach($permission);
        }

        // 4. Seeder gán vai trò 'user' cho tất cả người dùng
        $users = User::all();
        $roleUser = Role::where('name', 'user')->first();

        foreach ($users as $user) {
            $user->roles()->attach($roleUser);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Kiểm tra nếu email này đã tồn tại
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Đặt mật khẩu cho admin
            ]
        );

        // Lấy role 'admin'
        $adminRole = Role::where('name', 'admin')->first();

        // Gán role 'admin' cho user
        if ($adminRole) {
            $admin->roles()->attach($adminRole);
        }
    }
}

