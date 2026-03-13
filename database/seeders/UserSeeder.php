<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name'              => 'shoppyadminer',
                'email'             => 'admin@shoppy.local',
                'email_verified_at' => now(),
                'password'          => Hash::make('1234'),
                'role'              => 'admin',
                'is_active'         => true,
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'María López',
                'email'             => 'maria@shoppy.local',
                'email_verified_at' => now(),
                'password'          => Hash::make('1234'),
                'role'              => 'seller',
                'is_active'         => true,
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Carlos Ramírez',
                'email'             => 'carlos@shoppy.local',
                'email_verified_at' => now(),
                'password'          => Hash::make('1234'),
                'role'              => 'seller',
                'is_active'         => true,
                'remember_token'    => Str::random(10),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
