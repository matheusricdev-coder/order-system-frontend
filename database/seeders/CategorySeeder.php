<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CategorySeeder extends Seeder
{
    /** Fixed UUIDs so re-seeding is idempotent. */
    private const CATEGORIES = [
        ['id' => '11111111-0000-0000-0000-000000000001', 'name' => 'Eletrônicos'],
        ['id' => '11111111-0000-0000-0000-000000000002', 'name' => 'Informática'],
        ['id' => '11111111-0000-0000-0000-000000000003', 'name' => 'Games'],
        ['id' => '11111111-0000-0000-0000-000000000004', 'name' => 'Moda'],
        ['id' => '11111111-0000-0000-0000-000000000005', 'name' => 'Calçados'],
        ['id' => '11111111-0000-0000-0000-000000000006', 'name' => 'Casa e Decoração'],
        ['id' => '11111111-0000-0000-0000-000000000007', 'name' => 'Eletrodomésticos'],
        ['id' => '11111111-0000-0000-0000-000000000008', 'name' => 'Esportes e Lazer'],
        ['id' => '11111111-0000-0000-0000-000000000009', 'name' => 'Beleza e Saúde'],
        ['id' => '11111111-0000-0000-0000-000000000010', 'name' => 'Alimentos e Bebidas'],
        ['id' => '11111111-0000-0000-0000-000000000011', 'name' => 'Livros e Papelaria'],
        ['id' => '11111111-0000-0000-0000-000000000012', 'name' => 'Brinquedos'],
        ['id' => '11111111-0000-0000-0000-000000000013', 'name' => 'Automotivo'],
        ['id' => '11111111-0000-0000-0000-000000000014', 'name' => 'Ferramentas'],
        ['id' => '11111111-0000-0000-0000-000000000015', 'name' => 'Animais de Estimação'],
    ];

    public function run(): void
    {
        $now = now()->toDateTimeString();

        $rows = array_map(
            static fn (array $cat): array => [
                ...$cat,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            self::CATEGORIES,
        );

        DB::table('categories')->upsert(
            $rows,
            ['id'],          // unique key
            ['name', 'updated_at'],
        );

        $this->command->info('✔ ' . count(self::CATEGORIES) . ' categorias inseridas/atualizadas.');
    }

    /** Returns the list of seeded categories for use in other seeders. */
    public static function categories(): array
    {
        return self::CATEGORIES;
    }
}
