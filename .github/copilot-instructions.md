# Laravel & PHP Development Standards (Suit Members)

## Technology Stack

- **Language**: PHP 8.3+ (Focus on modern features)
- **Framework**: Laravel 12.x+
- **Database**: MySQL 8.0+
- **Environment**: Docker (Container: `suit_members_fpm`)

## Coding Standards & Quality

- **Strict Typing**: Sempre utilize `declare(strict_types=1);` no topo de cada arquivo.
- **Análise Estática**: Compatibilidade total com PHPStan nível 6+.
- **Tipagem Forte**: Uso obrigatório de _return types_ e _argument types_ em todos os métodos e funções.
- **Code Style**: Seguir rigorosamente os padrões do **Laravel Pint** (Estilo Laravel/PSR-12).
- **Clean Code Tools**:
    - Utilize **Typed Properties** e **Constructor Property Promotion**.
    - Utilize **Arrow Functions** (`fn() => ...`) para lógicas simples e concisas.
    - Utilize operadores de coalescência nula (`??`) para simplificar condicionais.
    - Use sempre comparação idêntica (`===` e `!==`).

## Architecture & Best Practices

- **SOLID, DRY & KISS**: Priorize a simplicidade e a responsabilidade única.
- **Naming**: Nomes significativos e pronunciáveis (ex: `$currentDate` em vez de `$ymdstr`). Evite contextos desnecessários.
- **Thin Controllers**: Mantenha controllers magros. Utilize **FormRequests** para validações e **API Resources** para transformações de dados.
- **Flow Control**:
    - **Early Returns**: Retorne o mais cedo possível para evitar aninhamento de `if/else` e simplificar a lógica.
    - **Happy Path**: Trate exceções e erros primeiro, mantendo o fluxo principal linear.
- **Functions**: Limite argumentos de funções a 2 ou menos. Acima disso, utilize DTOs ou classes de configuração.

## Eloquent & Models (Modern Syntax)

- **Attribute Factory**: Nunca utilize os métodos antigos `get{Name}Attribute`. Utilize sempre `Illuminate\Database\Eloquent\Casts\Attribute`.
- **Method-Based Casts**: Defina casts através do método `protected function casts(): array` em vez da propriedade `$casts`.
- **Performance**: Utilize `->shouldCache()` em atributos calculados complexos.
- **Exemplo**:
    ```php
    protected function stockQuantity(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['stock'] ?? null,
            set: fn ($value) => ['stock' => $value],
        )->shouldCache();
    }
    ```

## Execution & Docker Workflow

- O projeto roda exclusivamente em Docker.
- **Comandos**: Sempre que sugerir comandos de terminal (artisan, phpunit, composer), formate-os para execução via container:
    - `docker exec -it suit_members_fpm php artisan [comando]`
    - `docker exec -it suit_members_fpm vendor/bin/phpunit`
- **Migrations**: Utilize `docker exec -i suit_members_fpm php artisan migrate`.
