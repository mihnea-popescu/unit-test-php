<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Ceainic electric',
                'description' => 'Ceainic electric din inox, capacitate 2 litri.',
                'price' => 150.00,
                'sale_price' => 135.00,
                'stock' => 80,
                'category_id' => 1, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Tocator de legume',
                'description' => 'Tocator manual de legume, cu lama din otel inoxidabil.',
                'price' => 50.00,
                'sale_price' => null,
                'stock' => 100,
                'category_id' => 1, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Set de farfurii porțelan',
                'description' => 'Set de 12 farfurii din porțelan, design modern.',
                'price' => 200.00,
                'sale_price' => 180.00,
                'stock' => 50,
                'category_id' => 2, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Set tacâmuri inox',
                'description' => 'Set de 24 tacâmuri din inox, include furculițe, cuțite, și linguri.',
                'price' => 300.00,
                'sale_price' => null,
                'stock' => 70,
                'category_id' => 2, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Vază decorativă',
                'description' => 'Vază din ceramică, înălțime 40 cm, pentru decor interior.',
                'price' => 120.00,
                'sale_price' => 100.00,
                'stock' => 30,
                'category_id' => 3, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Tablou peisaj',
                'description' => 'Tablou cu peisaj montan, dimensiuni 60x90 cm.',
                'price' => 250.00,
                'sale_price' => null,
                'stock' => 20,
                'category_id' => 3, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Set lenjerie de pat',
                'description' => 'Set lenjerie de pat pentru pat dublu, material bumbac 100%.',
                'price' => 350.00,
                'sale_price' => 315.00,
                'stock' => 40,
                'category_id' => 4, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Draperii opace',
                'description' => 'Draperii opace, culoare gri, dimensiuni 200x250 cm.',
                'price' => 400.00,
                'sale_price' => null,
                'stock' => 15,
                'category_id' => 4, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Lampadar modern',
                'description' => 'Lampadar cu design modern, înălțime ajustabilă, ideal pentru living.',
                'price' => 450.00,
                'sale_price' => 425.00,
                'stock' => 25,
                'category_id' => 5, 'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Aplică de perete',
                'description' => 'Aplică de perete cu lumina caldă, montare ușoară, potrivită pentru hol.',
                'price' => 150.00,
                'sale_price' => null,
                'stock' => 50,
                'category_id' => 5, 'created_at' => now(), 'updated_at' => now()
            ]
        ]);
    }
}
