<?php

declare(strict_types=1);

namespace App\Application\Commands;

use Frete\Core\Application\Command;

class AddProductToOrder extends Command
{
    public function __construct(public readonly string $productId, public readonly string $orderId)
    {
    }
}
