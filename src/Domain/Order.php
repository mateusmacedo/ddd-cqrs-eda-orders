<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\ProductItemAddedToOrder;
use App\Domain\Events\{OrderInitialized};
use ArrayObject;
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

    public function addProductItem(Product $product): void
    {
        $item = $this->items->offsetGet($product->id);
        $quantity = $item?->quantity ?? 0;

        $item = new Item($product->id, ++$quantity, $product->price);
        $this->items->offsetSet($product->id, $item);

        $this->addEvent(
            new ProductItemAddedToOrder(
                $this->id,
                [
                    'productId' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price
                ]
            )
        );
    }
}
