<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
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
        //$this->call(CompanySeeder::class);
        $this->call(CatItemSeeder::class);

    }
}
