<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view dashboard',
            'approve teachers',
            'revoke teachers',
            'create result',
            'edit result',
            'delete result',
            'view result',
            'view sessions',
            'create sessions',
            'edit sessions',
            'delete sessions',
            'create subjects',
            'edit subjects',
            'view subjects',
            'delete subjects',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $teacher = Role::firstOrCreate(['name' => 'Teacher']);
        $coordinator = Role::firstOrCreate(['name' => 'Coordinator']);
        $student = Role::firstOrCreate(['name' => 'Student']);

        // Assign permissions to Admin - All permissions
        $admin->givePermissionTo(Permission::all());

        $coordinatorPermissions = [
            'view dashboard',
            'create result',
            'edit result',
            'delete result',
            'view result',
            'view sessions',
            'create sessions',
            'edit sessions',
            'delete sessions',
            'create subjects',
            'edit subjects',
            'view subjects',
            'delete subjects',
        ];
        $coordinator->givePermissionTo($coordinatorPermissions);

        // Assign permissions to Teacher
        $teacher->givePermissionTo([
            'view dashboard',
            'create result',
            'edit result',
            'delete result',
            'view result',
        ]);

        // Assign permissions to Student
        $student->givePermissionTo([
            'view result'
        ]);
    }
}