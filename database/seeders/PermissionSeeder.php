<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'user-insert', 'user-edit', 'user-delete', 'user-list', 'user-get',
            'employee-insert', 'employee-edit', 'employee-delete', 'employee-list', 'employee-edit-by-id', 'employee-get'
        ];

        foreach ($permissions as $permission){
            Permission::create(['name' => $permission]);
        }
    }
}
