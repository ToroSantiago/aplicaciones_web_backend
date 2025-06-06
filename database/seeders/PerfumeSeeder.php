<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfume;

class PerfumeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perfumes = [
            [
                'nombre' => 'Sauvage',
                'marca' => 'Dior',
                'descripcion' => 'Una fragancia aromática y fresca con notas de bergamota y pimienta.',
                'volumen' => 100,
                'precio' => 125000,
                'genero' => 'M',
                'stock' => 15,
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/sauvage.jpg'
            ],
            [
                'nombre' => 'Coco Mademoiselle',
                'marca' => 'Chanel',
                'descripcion' => 'Elegante y sofisticada, con notas de naranja, jazmín y pachulí.',
                'volumen' => 50,
                'precio' => 95000,
                'genero' => 'F',
                'stock' => 20,
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/coco-mademoiselle.jpg'
            ],
            [
                'nombre' => 'One Million',
                'marca' => 'Paco Rabanne',
                'descripcion' => 'Fragancia especiada y dulce con notas de canela, cuero y ámbar.',
                'volumen' => 100,
                'precio' => 85000,
                'genero' => 'M',
                'stock' => 10,
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/one-million.jpg'
            ],
            [
                'nombre' => 'La Vie Est Belle',
                'marca' => 'Lancôme',
                'descripcion' => 'Dulce y floral, con iris, pachulí y notas gourmand.',
                'volumen' => 75,
                'precio' => 110000,
                'genero' => 'F',
                'stock' => 25,
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/la-vie-est-belle.jpg'
            ],
            [
                'nombre' => 'CK One',
                'marca' => 'Calvin Klein',
                'descripcion' => 'Fresca y cítrica, perfecta para el día a día. Una fragancia unisex icónica.',
                'volumen' => 200,
                'precio' => 65000,
                'genero' => 'U',
                'stock' => 30,
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/ck-one.jpg'
            ],
            [
                'nombre' => 'Black Opium',
                'marca' => 'Yves Saint Laurent',
                'descripcion' => 'Adictiva y sensual, con café, vainilla y flor de naranjo.',
                'volumen' => 90,
                'precio' => 120000,
                'genero' => 'F',
                'stock' => 0,
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/black-opium.jpg'
            ]
        ];

        foreach ($perfumes as $perfume) {
            Perfume::create($perfume);
        }
    }
}