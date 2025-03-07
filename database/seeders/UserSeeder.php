<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'phone' => 12341234,
            'profile' => 1,
            'status' => 1, 
            'image' => 'miimagen.jpg',
            'tema' => 0, 
            'email' => 'admin@admin.com',
            'password' => '12341234',
        ]);

        User::factory()->create([
            'name' => 'seller',
            'phone' => 12341234,
            'profile' => 1,
            'status' => 1, 
            'image' => 'noimg.png',
            'tema' => 0, 
            'email' => 'seller@gmail.com',
            'password' => '12341234',
        ]);
    }
}