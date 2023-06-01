<?php

declare(strict_types=1);

namespace App\Domain;

class Item
{
    public function __construct(
        public readonly string $productId,
        public readonly int $quantity,
        public readonly float $price,
    ) {
    }
}
