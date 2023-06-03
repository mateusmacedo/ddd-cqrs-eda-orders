<?php

declare(strict_types=1);

namespace App\Domain;

use Frete\Core\Infrastructure\Database\Errors\RepositoryError;

interface OrderRepository
{
    public function get(string $orderId): Order|RepositoryError;

    public function find(array $filters): array|RepositoryError;

    public function save(Order $order): Order|RepositoryError;
}
