<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CompanySeeder extends Seeder
{
    private const COMPANIES = [
        ['id' => '07e8a754-1893-437c-9d14-389c14a28d5c', 'trade_name' => 'TechZone'],
        ['id' => '1595b318-57e3-4246-9a71-ec8d7dd62e62', 'trade_name' => 'FashionStore'],
        ['id' => '3208ecd5-a5d0-45aa-bf1b-78d6a93e9402', 'trade_name' => 'GadgetHub'],
        ['id' => '61304e32-bf1d-4ac0-90f3-5532c825f6cc', 'trade_name' => 'GamersWorld'],
        ['id' => '914a8118-7f31-4af2-94e4-e9dd4a8a3126', 'trade_name' => 'HomeDecor'],
        ['id' => 'aca57700-7aa9-45d4-93c3-8d7aa8ef3875', 'trade_name' => 'SportLife'],
        ['id' => 'ef2ce8c2-d777-4aaf-a5de-ed34d93287ed', 'trade_name' => 'BeautyPlus'],
    ];

    public static function ids(): array
    {
        return array_column(self::COMPANIES, 'id');
    }

    public function run(): void
    {
        $now = now()->toDateTimeString();
        $rows = array_map(static fn ($c) => [...$c, 'created_at' => $now, 'updated_at' => $now], self::COMPANIES);
        DB::table('companies')->upsert($rows, ['id'], ['trade_name', 'updated_at']);
        $this->command->info('✔ ' . count(self::COMPANIES) . ' empresas inseridas/atualizadas.');
    }
}
