<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::firstOrCreate([
            'code' => Str::upper('MAD'),
        ], [
            'name'               => 'Dirham Marocain',
            'symbol'             => 'DH',
            'thousand_separator' => ',',
            'decimal_separator'  => '.',
            'exchange_rate'      => null,
        ]);

        Currency::firstOrCreate([
            'code' => Str::upper('USD'),
        ], [
            'name'               => 'United States Dollar',
            'symbol'             => '$',
            'thousand_separator' => ',',
            'decimal_separator'  => '.',
            'exchange_rate'      => null,
        ]);

        Currency::firstOrCreate([
            'code' => Str::upper('EUR'),
        ], [
            'name'               => 'Euro',
            'symbol'             => '€',
            'thousand_separator' => ',',
            'decimal_separator'  => '.',
            'exchange_rate'      => null,
        ]);

        Currency::firstOrCreate([
            'code' => Str::upper('BRL'),
        ], [
            'name'               => 'Real Brasileiro',
            'symbol'             => 'R$',
            'thousand_separator' => '.',
            'decimal_separator'  => ',',
            'exchange_rate'      => null,
        ]);
    }
}
