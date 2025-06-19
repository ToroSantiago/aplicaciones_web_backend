<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perfume;
use App\Models\PerfumeVariante;

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
                'genero' => 'M',
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/sauvage.jpg',
                'variantes' => [
                    ['volumen' => 75, 'precio' => 95000.50, 'stock' => 20],
                    ['volumen' => 100, 'precio' => 125000.00, 'stock' => 15],
                    ['volumen' => 200, 'precio' => 185500.99, 'stock' => 8]
                ]
            ],
            [
                'nombre' => 'Coco Mademoiselle',
                'marca' => 'Chanel',
                'descripcion' => 'Elegante y sofisticada, con notas de naranja, jazmín y pachulí.',
                'genero' => 'F',
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/coco-mademoiselle.jpg',
                'variantes' => [
                    ['volumen' => 75, 'precio' => 110500.00, 'stock' => 15],
                    ['volumen' => 100, 'precio' => 145750.50, 'stock' => 10],
                    ['volumen' => 200, 'precio' => 220999.99, 'stock' => 5]
                ]
            ],
            [
                'nombre' => 'One Million',
                'marca' => 'Paco Rabanne',
                'descripcion' => 'Fragancia especiada y dulce con notas de canela, cuero y ámbar.',
                'genero' => 'M',
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/one-million.jpg',
                'variantes' => [
                    ['volumen' => 75, 'precio' => 65299.90, 'stock' => 18],
                    ['volumen' => 100, 'precio' => 85500.00, 'stock' => 10],
                    ['volumen' => 200, 'precio' => 125999.95, 'stock' => 6]
                ]
            ],
            [
                'nombre' => 'La Vie Est Belle',
                'marca' => 'Lancôme',
                'descripcion' => 'Dulce y floral, con iris, pachulí y notas gourmand.',
                'genero' => 'F',
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/la-vie-est-belle.jpg',
                'variantes' => [
                    ['volumen' => 75, 'precio' => 85750.00, 'stock' => 22],
                    ['volumen' => 100, 'precio' => 110990.90, 'stock' => 25],
                    ['volumen' => 200, 'precio' => 165500.00, 'stock' => 12]
                ]
            ],
            [
                'nombre' => 'CK One',
                'marca' => 'Calvin Klein',
                'descripcion' => 'Fresca y cítrica, perfecta para el día a día. Una fragancia unisex icónica.',
                'genero' => 'U',
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/ck-one.jpg',
                'variantes' => [
                    ['volumen' => 75, 'precio' => 45599.99, 'stock' => 35],
                    ['volumen' => 100, 'precio' => 55750.00, 'stock' => 30],
                    ['volumen' => 200, 'precio' => 85999.90, 'stock' => 20]
                ]
            ],
            [
                'nombre' => 'Black Opium',
                'marca' => 'Yves Saint Laurent',
                'descripcion' => 'Adictiva y sensual, con café, vainilla y flor de naranjo.',
                'genero' => 'F',
                'imagen_url' => 'https://res.cloudinary.com/demo/image/upload/v1/perfumes/black-opium.jpg',
                'variantes' => [
                    ['volumen' => 75, 'precio' => 95899.50, 'stock' => 0],
                    ['volumen' => 100, 'precio' => 120750.00, 'stock' => 0],
                    ['volumen' => 200, 'precio' => 180999.99, 'stock' => 2]
                ]
            ]
        ];

        foreach ($perfumes as $perfumeData) {
            $variantes = $perfumeData['variantes'];
            unset($perfumeData['variantes']);
            
            $perfume = Perfume::create($perfumeData);
            
            foreach ($variantes as $variante) {
                $perfume->variantes()->create($variante);
            }
        }
    }
}