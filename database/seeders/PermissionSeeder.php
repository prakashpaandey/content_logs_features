<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            ['name' => 'View Clients', 'slug' => 'clients.view', 'module' => 'Clients'],
            ['name' => 'Add Clients', 'slug' => 'clients.create', 'module' => 'Clients'],
            ['name' => 'Edit Clients', 'slug' => 'clients.update', 'module' => 'Clients'],
            ['name' => 'Delete Clients', 'slug' => 'clients.delete', 'module' => 'Clients'],

            // Granular Content Operations (New)
            ['name' => 'View Content', 'slug' => 'contents.view', 'module' => 'Content'],
            ['name' => 'Add Content', 'slug' => 'contents.create', 'module' => 'Content'],
            ['name' => 'Edit Content', 'slug' => 'contents.update', 'module' => 'Content'],
            ['name' => 'Delete Content', 'slug' => 'contents.delete', 'module' => 'Content'],

            // Granular Boost Tracking (New)
            ['name' => 'View Boosts', 'slug' => 'boosts.view', 'module' => 'Boosts'],
            ['name' => 'Add Boosts', 'slug' => 'boosts.create', 'module' => 'Boosts'],
            ['name' => 'Edit Boosts', 'slug' => 'boosts.update', 'module' => 'Boosts'],
            ['name' => 'Delete Boosts', 'slug' => 'boosts.delete', 'module' => 'Boosts'],

            // Target Management
            ['name' => 'View Targets', 'slug' => 'targets.view', 'module' => 'Targets'],
            ['name' => 'Manage Targets', 'slug' => 'targets.manage', 'module' => 'Targets'],
        ];

        // Explicit sync loop: Find by unique slug, then insert or update
        foreach ($permissions as $data) {
            $permission = \App\Models\Permission::where('slug', $data['slug'])->first();
            
            if ($permission) {
                // existing record update
                $permission->update($data);
            } else {
                // New record insert
                \App\Models\Permission::create($data);
            }
        }

        // Assign Admin role
        $adminEmail = 'info@bihanitech.com';
        $adminUser = \App\Models\User::where('email', $adminEmail)->first();
        
        if ($adminUser) {
            $adminUser->update([
                'role' => 'admin',
                'status' => 'active'
            ]);
        }
    }
}
