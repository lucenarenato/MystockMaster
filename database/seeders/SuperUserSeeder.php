<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('volumes')->updateOrInsert(
            ['nome' => 'Local Disk'],
            [
                'caminho'    => 'storage/app',
                'driver'     => 'local',
                'config'     => json_encode(['root' => storage_path('app'), 'url' => '/storage']),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'tenant-owner'],
            [
                'uuid'      => Str::uuid()->toString(),
                'name'      => 'Tenant Owner',
                'descricao' => 'Tenant do dono do sistema',
                'ativo'     => true,
                'ordem'     => 1,
            ]
        );

        $tenantUser = User::updateOrCreate(
            ['email' => 'tenant.owner@gmail.com'],
            [
                'id'                => 2,
                'uuid'              => Str::uuid()->toString(),
                'name'              => 'Tenant Owner',
                'password'          => bcrypt('password'),
                'avatar'            => 'avatar.png',
                'phone'             => '0123456789',
                'role_id'           => 1,
                'tenant_id'         => $tenant->id,
                'status'            => 1,
                'is_all_warehouses' => 0,
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        $tenantUser->assignRole('Super Admin');

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'id'                => 1,
                'uuid'              => Str::uuid()->toString(),
                'name'              => 'Admin',
                'password'          => bcrypt('password'),
                'avatar'            => 'avatar.png',
                'phone'             => '0123456789',
                'role_id'           => 1,
                'tenant_id'         => null,
                'status'            => 1,
                'is_all_warehouses' => 1,
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        $admin->assignRole('Super Admin');
    }
}
