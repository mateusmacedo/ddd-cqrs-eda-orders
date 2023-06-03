<?php

declare(strict_types=1);

namespace App\Domain;

use Frete\Core\Infrastructure\Database\Errors\RepositoryError;

interface ProductRepository
{
    public function get(string $productId): Product|RepositoryError;

    public function find(array $filters): array|RepositoryError;

    public function save(Product $product): Product|RepositoryError;
}
