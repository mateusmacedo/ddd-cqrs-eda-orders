<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use Frete\Core\Domain\AggregateRoot;

class Product extends AggregateRoot
{
    public function __construct(
        string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly DateTimeImmutable $registeredAt = new DateTimeImmutable(),
    ) {
        parent::__construct($id);
    }
}
