<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CatItem;

class CatItemSeeder extends Seeder
{
    public function run()
    {
        // Perfiles
        CatItem::insert([
            ['category' => 'profile', 'code' => 0, 'description' => 'Admin'],
            ['category' => 'profile', 'code' => 1, 'description' => 'Employee'],
            ['category' => 'profile', 'code' => 2, 'description' => 'Seller'],
        ]);

        // Estados
        CatItem::insert([
            ['category' => 'status', 'code' => 0, 'description' => 'Active'],
            ['category' => 'status', 'code' => 1, 'description' => 'Inactive'],
            ['category' => 'status', 'code' => 2, 'description' => 'Locked'],
        ]);
    }
}
