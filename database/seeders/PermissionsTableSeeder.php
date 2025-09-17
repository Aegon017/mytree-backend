<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'dashboard','contact_us','users_list',
            'tree_list','tree_view','tree_insert','tree_delete','tree_update','tree_status',
            'fund_tree_list','fund_tree_view','fund_tree_insert','fund_tree_delete','fund_tree_update','fund_tree_status',
            'adopt_tree_list','adopt_tree_view','adopt_tree_insert','adopt_tree_delete','adopt_tree_update','adopt_tree_status',
            'adopted_tree_list','adopted_tree_view','adopted_tree_insert','adopted_tree_delete','adopted_tree_update','adopted_tree_status',
            'order_list','order_view','order_insert','order_delete','order_update','order_status',
            'blog_list','blog_view','blog_insert','blog_delete','blog_update','blog_status',
            'employee_list','employee_view','employee_insert','employee_delete','employee_update','employee_status',
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName,'guard_name'=>'admin']);
        }

         // Create the permission
         $adminPermission = $permissions;
         $supervisorsPermission = ['dashboard','contact_us','users_list','order_list','order_view','order_insert','order_delete','order_update','order_status'];
         $accountantsPermission = ['order_list','order_view','order_insert','order_delete','order_update','order_status',];
         $managerPermission = $permissions;
         $telecallerPermission = ['users_list', 'order_list'];

         // Find the roles
         $adminRole = Role::findByName('super-admin','admin');
         $supervisorsRole = Role::findByName('supervisors','admin');
         $accountantsRole = Role::findByName('accountants','admin');
         $managerRole = Role::findByName('manager','admin');
         $telecallerRole = Role::findByName('telecaller','admin');
 
         // Assign permission to roles
        //  $editorRole->givePermissionTo(['edit articles', 'view articles']);
         $adminRole->givePermissionTo($adminPermission);
         $supervisorsRole->givePermissionTo($supervisorsPermission);
         $accountantsRole->givePermissionTo($accountantsPermission);
         $managerRole->givePermissionTo($managerPermission);
         $telecallerRole->givePermissionTo($telecallerPermission);
    }
}
