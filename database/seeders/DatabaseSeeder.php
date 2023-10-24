<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Role::create([
            'name' => 'Admin',
            'slug' => 'admin'
        ]);

        Role::create([
            'name' => 'Manager',
            'slug' => 'manager'
        ]);

        Role::create([
            'name' => 'User',
            'slug' => 'user'
        ]);

        User::factory()->create([
            'role_id' => 1,
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'role_id' => 2,
            'email' => 'manager@gmail.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'role_id' => 3,
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
        ]);

        UserDetail::create([
            'user_id' => 1,
            'first_name' => 'Dobith',
            'last_name' => 'Riyadi',
            'address' => 'Ciamis',
            'gender' => 'Male',
            'date_of_birth' => '2023-10-24',
        ]);

        UserDetail::create([
            'user_id' => 2,
            'first_name' => 'Dobith',
            'last_name' => 'Riyadi',
            'address' => 'Ciamis',
            'gender' => 'Male',
            'date_of_birth' => '2023-10-24',
        ]);

        UserDetail::create([
            'user_id' => 3,
            'first_name' => 'Dobith',
            'last_name' => 'Riyadi',
            'address' => 'Ciamis',
            'gender' => 'Male',
            'date_of_birth' => '2023-10-24',
        ]);
    }
}
