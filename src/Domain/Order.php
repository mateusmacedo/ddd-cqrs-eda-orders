<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\{OrderInitialized};
use ArrayObject;
use Exception;
use Frete\Core\Domain\AggregateRoot;

final class Order extends AggregateRoot
{
    public function __construct(
        string $id,
        public readonly ArrayObject $items,
        public readonly string $createdAt,
    ) {
        parent::__construct($id);
        $this->items ??= new ArrayObject();
        $this->createdAt ??= date('Y-m-d H:i:s');
    }

    public static function init(string $id): Order
    {
        $order = new Order($id, new ArrayObject(), date('Y-m-d H:i:s'));
        $order->addEvent(new OrderInitialized($id, [
            'items' => $order->items->getArrayCopy(),
            'createdAt' => $order->createdAt,
        ]));

        return $order;
    }
}
