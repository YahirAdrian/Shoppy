<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name'        => 'Bebidas',
                'description' => 'Refrescos, jugos, agua y bebidas embotelladas',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Snacks',
                'description' => 'Papas, frituras, galletas y botanas',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Lácteos',
                'description' => 'Leche, yogur, queso y derivados',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Panadería',
                'description' => 'Pan dulce, pan de caja y bollería',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Limpieza',
                'description' => 'Detergentes, desinfectantes y artículos de limpieza',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Abarrotes',
                'description' => 'Arroz, frijol, azúcar, aceite y productos básicos',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
