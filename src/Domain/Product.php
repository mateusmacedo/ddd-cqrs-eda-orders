<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\ProductRegistered;
use DateTimeImmutable;
use Frete\Core\Domain\AggregateRoot;

class Product extends AggregateRoot
{
    public function __construct(
        string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        parent::__construct($id);
    }

    public static function register(
        string $id,
        string $name,
        string $description,
        float $price,
    ): Product {
        $product = new Product($id, $name, $description, $price);
        $product->addEvent(new ProductRegistered($id, [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'createdAt' => $product->createdAt->format('Y-m-d H:i:s'),
        ]));

        return $product;
    }
}
