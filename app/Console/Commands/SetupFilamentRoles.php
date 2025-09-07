<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Filament\Shield\Support\Utils;

class SetupFilamentRoles extends Command
{
    protected $signature = 'setup:install-app-compras';
    protected $description = 'Instala roles, permisos y crea un super usuario para Filament con Shield';

    public function handle()
    {
        $this->info('🔐 Creando roles...');

        $roles = [
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'cliente', 'guard_name' => 'client'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate($roleData);
            $this->info("✅ Rol '{$roleData['name']}' creado con guard '{$roleData['guard_name']}'");
        }

        $this->info('🛡️ Generando permisos con Filament Shield...');

        // Genera permisos para el guard web
        $this->info('🛡️ Generando permisos con Filament Shield...');
        $this->call('shield:generate', ['--all' => true, '--option' => 'permissions' , '--panel' => 'admin']);
        $this->info('✅ Permisos generados con éxito');

        $this->info('👤 Creando super usuario...');
      

        $user = User::firstOrCreate(
            ['email' => 'jcenrique@free.fr'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('1234qwer'),
                'email_verified_at' => now(),
            ]
        );
    $this->call('shield:super-admin', ['--user' => $user->id]);
        //$user->assignRole('usuario');
        $this->info("🎉 Usuario '{$user->email}' creado y asignado al rol 'usuario'");
          return Command::SUCCESS;
    }
}
