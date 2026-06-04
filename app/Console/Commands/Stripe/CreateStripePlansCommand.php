<?php

declare(strict_types=1);

namespace App\Console\Commands\Stripe;

use Illuminate\Console\Command;
use Stripe\StripeClient;

class CreateStripePlansCommand extends Command
{
    protected $signature = 'stripe:create-plans
        {--save-env : Salvar os IDs de preço gerados no arquivo .env}
        {--force : Forçar sobrescrever valores existentes em .env}
        {--currency=brl : Moeda usada para os preços Stripe}
        {--interval=month : Intervalo de cobrança recorrente na Stripe}';

    protected $description = 'Cria produtos e preços de assinatura na Stripe e imprime os STRIPE_PRICE_* para o .env.';

    public function handle(): int
    {
        $stripeSecret = env('STRIPE_SECRET');

        if (! is_string($stripeSecret) || $stripeSecret === '') {
            $this->error('A variável STRIPE_SECRET não está configurada no .env.');

            return self::FAILURE;
        }

        $plans = config('plans');

        if (! is_array($plans) || count($plans) === 0) {
            $this->error('O arquivo de configuração config/plans.php não está disponível ou está vazio.');

            return self::FAILURE;
        }

        $currency = (string) $this->option('currency');
        $interval = (string) $this->option('interval');
        $saveEnv = (bool) $this->option('save-env');
        $force = (bool) $this->option('force');

        $client = new StripeClient($stripeSecret);

        $this->info('Criando produtos e preços na Stripe para os planos configurados em config/plans.php');

        $rows = [];
        $envValues = [];

        foreach ($plans as $key => $plan) {
            $planName = $plan['name'] ?? ucfirst($key);
            $description = sprintf('Plano %s do MyStockMaster', $planName);

            $amountInCents = $this->askPrice($planName);

            $product = $this->findOrCreateProduct($client, $planName, $description);
            $price = $this->findOrCreatePrice($client, $product->id, $amountInCents, $currency, $interval);

            $envKey = 'STRIPE_PRICE_' . strtoupper($key);
            $envValues[$envKey] = $price->id;

            $rows[] = [
                'Plano' => ucfirst($key),
                'Nome' => $planName,
                'Valor' => 'R$ ' . number_format($amountInCents / 100, 2, ',', '.'),
                'Produto Stripe' => $product->id,
                'Price ID' => $price->id,
                'Env key' => $envKey,
            ];
        }

        $this->table(['Plano', 'Nome', 'Valor', 'Produto Stripe', 'Price ID', 'Env key'], $rows);

        $this->line('');
        $this->info('Copie os valores abaixo para o arquivo .env ou use a opção --save-env:');

        foreach ($envValues as $key => $value) {
            $this->line(sprintf('%s=%s', $key, $value));
        }

        if ($saveEnv || $this->confirm('Deseja salvar automaticamente estes valores em .env?')) {
            $this->writeEnvironmentValues($envValues, $force);
            $this->info('Arquivo .env atualizado com os valores STRIPE_PRICE_* existentes.');
        }

        return self::SUCCESS;
    }

    protected function askPrice(string $planName): int
    {
        while (true) {
            $answer = $this->ask(sprintf('Informe o valor mensal em reais para o plano %s (ex: 19.90)', $planName));
            $answer = trim((string) $answer);
            $answer = str_replace(',', '.', $answer);

            if ($answer === '') {
                $this->error('O valor não pode ficar em branco.');

                continue;
            }

            if (! preg_match('/^\d+(\.\d{1,2})?$/', $answer)) {
                $this->error('Valor inválido. Use apenas números e até duas casas decimais, ex: 19.90');

                continue;
            }

            return (int) round((float) $answer * 100);
        }
    }

    protected function findOrCreateProduct(StripeClient $client, string $name, string $description): object
    {
        $existing = $client->products->all(['limit' => 100]);

        foreach ($existing->data as $product) {
            if (isset($product->name) && $product->name === $name) {
                $this->line("Produto Stripe já existe: {$name} ({$product->id}). Usando produto existente.");

                return $product;
            }
        }

        return $client->products->create([
            'name' => $name,
            'description' => $description,
            'metadata' => [
                'app' => 'mystockmaster',
                'plan_key' => strtolower($name),
            ],
        ]);
    }

    protected function findOrCreatePrice(StripeClient $client, string $productId, int $amount, string $currency, string $interval): object
    {
        $prices = $client->prices->all(['product' => $productId, 'limit' => 100]);

        foreach ($prices->data as $price) {
            if (isset($price->unit_amount)
                && (int) $price->unit_amount === $amount
                && isset($price->currency)
                && $price->currency === $currency
                && isset($price->recurring)
                && $price->recurring->interval === $interval
            ) {
                $this->line(sprintf('Price Stripe já existe para %s: %s', $productId, $price->id));

                return $price;
            }
        }

        return $client->prices->create([
            'unit_amount' => $amount,
            'currency' => $currency,
            'recurring' => ['interval' => $interval],
            'product' => $productId,
            'metadata' => [
                'app' => 'mystockmaster',
            ],
        ]);
    }

    protected function writeEnvironmentValues(array $values, bool $force): void
    {
        $envPath = base_path('.env');

        if (! is_file($envPath)) {
            file_put_contents($envPath, '');
        }

        $contents = file_get_contents($envPath) ?: '';

        foreach ($values as $key => $value) {
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

            if (preg_match($pattern, $contents, $matches)) {
                if (! $force && trim(explode('=', $matches[0], 2)[1] ?? '') !== '') {
                    if (! $this->confirm("A variável {$key} já existe em .env. Deseja sobrescrevê-la?")) {
                        continue;
                    }
                }

                $contents = preg_replace($pattern, sprintf('%s=%s', $key, $value), $contents);
            } else {
                $contents .= PHP_EOL . sprintf('%s=%s', $key, $value) . PHP_EOL;
            }
        }

        file_put_contents($envPath, $contents);
    }
}
