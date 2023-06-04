<?php

declare(strict_types=1);

namespace App\Application\Commands;

use Frete\Core\Application\Command;

class PlaceOrder extends Command
{
    public function __construct(public readonly string $orderId)
    {
    }
}
