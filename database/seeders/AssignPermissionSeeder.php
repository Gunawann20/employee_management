<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPermissions = [
            'user-insert', 'user-edit', 'user-delete', 'user-list', 'user-get',
            'employee-insert', 'employee-edit', 'employee-delete', 'employee-list', 'employee-edit-by-id', 'employee-get'
        ];

        $adminHrPermissions = [
            'employee-insert', 'employee-edit', 'employee-list', 'employee-edit-by-id', 'employee-get'
        ];

        $karyawanPermissions = [
            'employee-edit', 'employee-get'
        ];

        $superAdmin = Role::findByName('super-admin');
        $superAdmin->givePermissionTo($adminPermissions);

        $adminHr = Role::findByName('admin-hr');
        $adminHr->givePermissionTo($adminHrPermissions);

        $karyawan = Role::findByName('karyawan');
        $karyawan->givePermissionTo($karyawanPermissions);
    }
}
