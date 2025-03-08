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
        /*CatItem::insert([
            ['category' => 'profile', 'code' => 1, 'description' => 'admin'],
            ['category' => 'profile', 'code' => 2, 'description' => 'seller'],
            ['category' => 'profile', 'code' => 3, 'description' => 'employee'],
        ]);*/

        // Estados
        CatItem::insert([
            ['category' => 'status', 'code' => 1, 'description' => 'active'],
            ['category' => 'status', 'code' => 2, 'description' => 'inactive'],
            ['category' => 'status', 'code' => 3, 'description' => 'locked'],
        ]);
    }
}
