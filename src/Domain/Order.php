<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\{OrderInitialized};
use App\Domain\Events\{ProductItemAddedToOrder, ProductItemRemovedFromOrder};
use ArrayObject;
use Frete\Core\Domain\AggregateRoot;

final class Order extends AggregateRoot
{
    public const IS_INIT = 'init';

    public function __construct(
        string $id,
        public readonly ArrayObject $items,
        public readonly string $createdAt,
        protected string $status = self::IS_INIT
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

    public function isInitialized(): bool
    {
        return self::IS_INIT === $this->status;
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

    public function removeProductItem(Product $product): void
    {
        $item = $this->items->offsetGet($product->id);
        if (null === $item) {
            return;
        }

        $quantity = (int) ($item->quantity - 1);
        if (0 === $quantity) {
            $this->items->offsetUnset($product->id);
        } else {
            $this->items->offsetSet($product->id, new Item($product->id, $quantity, $product->price));
        }

        $this->addEvent(new ProductItemRemovedFromOrder($this->id, ['productId' => $product->id]));
    }
}
