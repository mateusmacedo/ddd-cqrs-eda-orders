<?php

declare(strict_types=1);

namespace App\Application\Commands;

use Frete\Core\Application\Command;

class RegisterProduct extends Command
{
    public function __construct(public readonly string $productId, public readonly string $name, public readonly string $description, public readonly float $price)
    {
    }
}
