<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $permissions = [
            //admin dashboard
            'admin_panel_access',
            //user request page
            'user_approval',
            //user management functions
            'users_access',
            'user-user_assign_role',
            'user_delete',
            'user_show_details',
            //role page
            'roles_access',
            'role_edit',
            'role_delete',
            'role_create',
            'role_show',
            //permission access in the role page
            'permissions_access',
            'permission_edit',
            //company page access
            'company_access',
            'company_create',
            'company_update',
            'company_show_details',
            'company_delete',
            //equipment management
            'equipment_access',
            'category_access',
            'category_create',
            'specification_access',
            'specification_create',
            'specification_show_details',
            'specification_delete',
            'specification_update_btn',
            'unit_access',
            'unit_create',
            'unit_show_details',
            'unit_delete',
            'unit_update_btn',
            'status_access',
            'status_create',
            'status_show_details',
            'status_delete',
            'status_update_btn',
            'activity_log',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'super_admin']);
        $editorRole = Role::create(['name' => 'admin']);

        $adminRole->givePermissionTo($permissions);

        $editorRole->givePermissionTo([
            'status_access',
            'status_create',
            'status_show_details',
            'status_delete',
            'status_update_btn',
            'activity_log',
        ]);
    }
}
