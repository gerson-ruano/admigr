<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // User::factory(10)->create();

        $sellerRole = Role::firstOrCreate(['name' => 'seller']);

        $superadmin = User::factory()->create([
            'name' => 'administrador',
            'phone' => 12341234,
            'profile' => 'admin',
            'status' => 1, 
            'image' => 'miimagen.jpg',
            'tema' => 0, 
            'email' => 'admin@admin.com',
            'password' => bcrypt('12341234'),
        ]);
        // Verificar si el rol existe, si no, crearlo
        $role = Role::firstOrCreate(['name' => 'super_admin']);
        // Asignar el rol al usuario
        $superadmin->assignRole($role);

        $seller = User::factory()->create([
            'name' => 'luis ramirez',
            'phone' => 12341234,
            'profile' => 'seller',
            'status' => 1, 
            'image' => 'noimg.png',
            'tema' => 0, 
            'email' => 'seller@admin.com',
            'password' => '12341234',
        ]);
        $seller->assignRole($sellerRole);

        User::factory()->create([
            'name' => 'juan perez',
            'phone' => 12341234,
            'profile' => 'employee',
            'status' => 1, 
            'image' => 'noimg.png',
            'tema' => 0, 
            'email' => 'employee@admin.com',
            'password' => '12341234',
        ]);
    }
}