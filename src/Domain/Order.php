<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\{OrderInitialized};
use App\Domain\Events\{ProductItemAddedToOrder, ProductItemRemovedFromOrder};
use App\Domain\Events\OrderPlaced;
use ArrayObject;
use DateTimeImmutable;
use Frete\Core\Domain\AggregateRoot;

final class Order extends AggregateRoot
{
    public const IS_INIT = 'init';
    public const IS_PLACED = 'placed';

    public function __construct(
        string $id,
        protected ArrayObject $items = new ArrayObject(),
        protected string $status = self::IS_INIT,
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        parent::__construct($id);
    }

    public static function init(string $id): Order
    {
        $order = new Order($id);

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

    public function isPlaced(): bool
    {
        return self::IS_PLACED === $this->status;
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

    public function markOrderAsPlaced(): void
    {
        if ($this->isPlaced()) {
            throw new \DomainException('Order is already placed');
        }

        if ($this->items->count() === 0) {
            throw new \DomainException('Order must have at least one item to be placed');
        }

        $this->status = self::IS_PLACED;
        $this->addEvent(new OrderPlaced($this->id));
    }

    public function listProductItems(): array
    {
        return $this->items->getArrayCopy();
    }
}
