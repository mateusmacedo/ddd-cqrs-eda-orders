<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\OrderPlaced;
use App\Domain\Events\{OrderCreated};
use App\Domain\Events\{ProductItemAddedToOrder, ProductItemRemovedFromOrder};
use ArrayObject;
use DateTimeImmutable;
use DomainException;
use Frete\Core\Domain\AggregateRoot;
use Frete\Core\Domain\Errors\DomainError;

class Order extends AggregateRoot
{
    public const IS_INIT = 'initialized';
    public const IS_PLACED = 'placed';

    public function __construct(
        string $id,
        protected string $status = self::IS_INIT,
        public readonly DateTimeImmutable $initializedAt = new DateTimeImmutable(),
        protected ArrayObject $items = new ArrayObject(),
    ) {
        parent::__construct($id);
    }

    public function isInitialized(): bool
    {
        return self::IS_INIT === $this->status;
    }

    public function isPlaced(): bool
    {
        return self::IS_PLACED === $this->status;
    }

    public function addProductItem(Product $product): DomainError|bool
    {
        $quantity = 0;

        if ($this->items->offsetExists($product->id)) {
            $quantity = $this->items->offsetGet($product->id)->quantity;
        }

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

        return true;
    }

    public function removeProductItem(Product $product): DomainError|bool
    {
        if (!$this->items->offsetExists($product->id)) {
            return new DomainError('Product not found in order', 1);
        }

        $quantity = (int) $this->items->offsetGet($product->id)->quantity - 1;

        if (0 === $quantity) {
            $this->items->offsetUnset($product->id);
        } else {
            $this->items->offsetSet($product->id, new Item($product->id, $quantity, $product->price));
        }

        $this->addEvent(new ProductItemRemovedFromOrder($this->id, ['productId' => $product->id]));

        return true;
    }

    public function markOrderAsPlaced(): void
    {
        if ($this->isPlaced()) {
            throw new DomainException('Order is already placed');
        }

        if (0 === $this->items->count()) {
            throw new DomainException('Order must have at least one item to be placed');
        }

        $this->status = self::IS_PLACED;
        $this->addEvent(new OrderPlaced($this->id));
    }

    public function listProductItems(): array
    {
        return $this->items->getArrayCopy();
    }

    public function calculateTotalPrice(): float
    {
        $totalPrice = array_reduce(
            $this->items->getArrayCopy(),
            fn (float $total, Item $item) => $total + ($item->quantity * $item->price),
            0.0
        );
        return round($totalPrice, 2);
    }
}
