<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('business_settings')->insert([
            'business_name'  => 'La Tiendita',
            'logo'           => null,
            'address'        => 'Calle Hidalgo 42, Col. Centro',
            'phone'          => '555-123-4567',
            'email'          => 'contacto@latiendita.mx',
            'currency_symbol' => '$',
            'low_stock'      => 5,
            'receipt_header' => 'Bienvenido a La Tiendita',
            'receipt_footer' => '¡Gracias por su compra! Vuelva pronto.',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
