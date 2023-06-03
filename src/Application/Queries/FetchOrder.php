<?php

declare(strict_types=1);

namespace App\Application\Queries;

use Frete\Core\Application\Query;

class FetchOrder extends Query
{
    public function __construct(public readonly string $orderId)
    {
    }
}
