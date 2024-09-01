<?php

namespace Database\Seeders;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $permissions = array(
            ['name' => 'view-stock', 'display_name' => 'View Stock'],
            ['name' => 'add-expenses', 'display_name' => 'Add Expenses'],
            ['name' => 'view-expenses', 'display_name' => 'View Expenses'],
            ['name' => 'edit-expenses', 'display_name' => 'Edit Expenses'],
            ['name' => 'delete-expenses', 'display_name' => 'Delete Expenses'],
            ['name' => 'view-product', 'display_name' => 'View Products'],
            ['name' => 'add-product', 'display_name' => 'Add Product'],
            ['name' => 'edit-product', 'display_name' => 'Edit Product'],
            ['name' => 'view-purchase', 'display_name' => 'View Purchase'],
            ['name' => 'add-purchase', 'display_name' => 'Add Purchase'],
            ['name' => 'delete-purchase', 'display_name' => 'Delete Purchase'],
            ['name' => 'view-supplier', 'display_name' => 'View Supplier'],
            ['name' => 'add-supplier', 'display_name' => 'Add Supplier'],
            ['name' => 'edit-supplier', 'display_name' => 'Edit Supplier'],
            ['name' => 'delete-supplier', 'display_name' => 'Delete Supplier'],
            ['name' => 'view-stock', 'display_name' => 'View Stock'],
            ['name' => 'edit-stock', 'display_name' => 'Edit Stock'],
            ['name' => 'delete-stock', 'display_name' => 'Delete Stock'],
            ['name' => 'add-cashin', 'display_name' => 'Add Cashin'],
            ['name' => 'edit-cashin', 'display_name' => 'Edit Cashin'],
            ['name' => 'delete-cashin', 'display_name' => 'Delete Cashin'],
            ['name' => 'add-cashout', 'display_name' => 'Add Cashout'],
            ['name' => 'delete-cashout', 'display_name' => 'Delete Cashout'],
            ['name' => 'edit-cashout', 'display_name' => 'Edit Cashout'],
            ['name' => 'edit-sales', 'display_name' => 'Edit Sales'],
            ['name' => 'delete-sales', 'display_name' => 'Delete Sales'],
            ['name' => 'create-sales', 'display_name' => 'Create Sales'],
            ['name' => 'view-sales', 'display_name' => 'View Sales'],
            ['name' => 'view-report', 'display_name' => 'View Report'],
            ['name' => 'add-customer', 'display_name' => 'Add Customers'],
            ['name' => 'edit-customer', 'display_name' => 'Edit Customers'],
            ['name' => 'delete-customer', 'display_name' => 'Delete Customers'],
            ['name' => 'create-view', 'display_name' => 'View Customers'],
            ['name' => 'manage-invoice', 'display_name' => 'Manage Invoice'],

            ['name' => 'edit-purchase', 'display_name' => 'Edit Purchase'],
            ['name' => 'add-transaction', 'display_name' => 'Add Transaction'],
            ['name' => 'view-cashflow', 'display_name' => 'View Cashflow'],
            ['name' => 'view-transaction', 'display_name' => 'View Transaction'],
            ['name' => 'edit-transaction', 'display_name' => 'Edit Transaction'],
            ['name' => 'delete-transaction', 'display_name' => 'Delete Transaction'],
            ['name' => 'delete-product', 'display_name' => 'Delete Product'],



        );

        foreach ($permissions as $key => $perm) {
            $permission = Permission::where('name', $perm['name'])->first();
            if (is_null($permission)) {
                Permission::create($perm);
            }
        }

        $roles = array(
            ['name' => 'super_admin', 'display_name' => 'Administrator', 'description' => 'Smartmauzo system Admin who monitors configuration, and reliable operation of Smart Mauzo App and its infrastracture'],
            ['name' => 'sales_representative', 'display_name' => 'Sales representative', 'description' => 'Smart Mauzo Sales representatives'],
            [
                'name' => 'joint_vent_partner', 'display_name' => 'Joint Venture Partner',
                'description' => 'All Managers of Smart Mauzo and Venture Partner'
            ],
            [
                'name' => 'manager', 'display_name' => 'Busness Owner/Manager',
                'description' => 'This role belongs to business owner or manager who have all privellages on his/her Smart Mauzo account', 'is_shop_role' => true
            ],
            ['name' => 'salesman', 'display_name' => 'Shop/Business Sales Person', 'description' => 'Thi role belongs to Shop/Business sales person'],
            [
                'name' => 'storekeeper', 'display_name' => 'Storekeeper',
                'description' => 'A person responsible for recording stored goods movements'
            ],
        );

        foreach ($roles as $key => $role) {
            $roleext = Role::where('name', $role['name'])->first();
            if (is_null($roleext)) {
                Role::create($role);
            }
        }
    }
}
