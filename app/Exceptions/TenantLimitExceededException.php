<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class TenantLimitExceededException extends RuntimeException
{
    public function __construct(string $resource, int $limit)
    {
        parent::__construct("Limite do plano atingido: {$resource} (máximo {$limit}).");
    }
}
