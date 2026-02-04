<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Client Management
            ['name' => 'View Clients', 'slug' => 'clients.view', 'module' => 'Clients'],
            ['name' => 'Add Clients', 'slug' => 'clients.create', 'module' => 'Clients'],
            ['name' => 'Edit Clients', 'slug' => 'clients.update', 'module' => 'Clients'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'module' => 'Clients'],

            // Content Operations
            ['name' => 'View Content', 'slug' => 'contents.view', 'module' => 'Content'],
            ['name' => 'Add Content', 'slug' => 'contents.create', 'module' => 'Content'],
            ['name' => 'Edit Content', 'slug' => 'contents.update', 'module' => 'Content'],
            ['name' => 'Delete Content', 'slug' => 'contents.delete', 'module' => 'Content'],

            // Boost Tracking
            ['name' => 'View Boosts', 'slug' => 'boosts.view', 'module' => 'Boosts'],
            ['name' => 'Add Boosts', 'slug' => 'boosts.create', 'module' => 'Boosts'],
            ['name' => 'Edit Boosts', 'slug' => 'boosts.update', 'module' => 'Boosts'],
            ['name' => 'Delete Boosts', 'slug' => 'boosts.delete', 'module' => 'Boosts'],

            // Target Settings
            ['name' => 'View Targets', 'slug' => 'targets.view', 'module' => 'Targets'],
            ['name' => 'Manage Targets', 'slug' => 'targets.manage', 'module' => 'Targets'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        // first user admin always
        $adminUser = \App\Models\User::first();
        if ($adminUser) {
            $adminUser->update(['role' => 'admin', 'status' => 'active']);
        }
    }
}
