<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'phone' => '0987876567',
            'password' => bcrypt('admin')
        ]);
        $user->assignRole('super-admin');
    }
}
