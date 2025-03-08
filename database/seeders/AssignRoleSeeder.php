<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignRoleSeeder extends Seeder
{
    public function run()
    {

        $user = User::where('email', 'admin@admin.com')->first();
        $role = Role::where('name', 'super_admin')->first();

        if ($user && $role) {
            $user->assignRole($role);
            $this->command->info("Rol '{$role->name}' asignado a {$user->name}.");
        } else {
            $this->command->error('Usuario o rol no encontrado.');
        }
    }


}
