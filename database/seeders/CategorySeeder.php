<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Bucătărie', 'description' => 'Produse ecologice și biodegradabile pentru bucătărie.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Decorațiuni', 'description' => 'Decorațiuni create din materiale reciclate.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mobilier', 'description' => 'Mobilier realizat din materiale sustenabile pentru a avea un impact redus asupra mediului.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Textile Naturale', 'description' => 'Textile realizate din materiale naturale, prietenoase cu mediul.', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
