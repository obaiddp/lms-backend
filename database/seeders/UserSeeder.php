<?php

namespace Database\Seeders;

use App\Models\Coordinator;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // LOCAL DEVELOPMENT ONLY - dummy data
        $users = [
            ['name' => 'Local Admin', 'email' => 'admin@local.test', 'role' => 1, 'role_name' => 'Admin'],
            ['name' => 'Local Instructor', 'email' => 'instructor@local.test', 'role' => 2, 'role_name' => 'Instructor'],
            ['name' => 'Local Student', 'email' => 'student@local.test', 'role' => 3, 'role_name' => 'Student'],
            ['name' => 'Local Coordinator', 'email' => 'coordinator@local.test', 'role' => 5, 'role_name' => 'Coordinator'],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => $data['role'],
                    'email_verified_at' => now(),
                    'phone_number' => '03000000000',
                    'address' => 'Local development address',
                    'is_public' => 1,
                ]
            );

            $user->syncRoles([$data['role_name']]);

            if ($data['role'] === 2) {
                Instructor::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'uuid' => (string) Str::uuid(),
                        'first_name' => 'Local',
                        'last_name' => 'Instructor',
                        'professional_title' => 'Development Instructor',
                        'slug' => 'local-instructor',
                        'gender' => 'other',
                        'status' => 1,
                    ]
                );
            }

            if ($data['role'] === 3) {
                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'uuid' => (string) Str::uuid(),
                        'first_name' => 'Local',
                        'last_name' => 'Student',
                        'gender' => 'other',
                        'status' => 1,
                        'type' => 1,
                    ]
                );
            }

            if ($data['role'] === 5) {
                Coordinator::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'uuid' => (string) Str::uuid(),
                        'first_name' => 'Local',
                        'last_name' => 'Coordinator',
                        'status' => 1,
                    ]
                );
            }
        }
    }
}