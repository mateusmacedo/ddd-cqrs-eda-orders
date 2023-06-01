<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Events\OrderPlaced;
use App\Domain\Events\{OrderInitialized, ProductItemAddedToOrder, ProductItemRemovedFromOrder};
use App\Domain\{Order, Product};
use ArrayObject;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private string $orderId;
    private ArrayObject $items;
    private Product $product;
    private Order $sut;

    protected function setUp(): void
    {
        $this->orderId = '123';
        $this->items = new ArrayObject();
        $this->sut = new Order($this->orderId);
        $this->product = new Product(...[
            'id' => '123',
            'name' => 'Product 1',
            'description' => 'Product 1 description',
            'price' => 10.00,
            'createdAt' => new DateTimeImmutable(),
        ]);
    }

    public function testCanAddProductOnOrder(): void
    {
        $this->sut->addProductItem($this->product);

        $this->assertNotEmpty($this->sut->listProductItems());

        $items = $this->sut->listProductItems();

        $this->assertArrayHasKey($this->product->id, $items);
        $this->assertSame(1, $items[$this->product->id]->quantity);
        $this->assertSame($this->product->price, $items[$this->product->id]->price);

        $events = $this->sut->getEvents();
        $this->assertNotEmpty($events);

        $productAddedToOrderEvent = array_shift($events);
        $this->sut->commitEvent($productAddedToOrderEvent);

        $this->assertInstanceOf(ProductItemAddedToOrder::class, $productAddedToOrderEvent);
        $this->assertSame($this->orderId, $productAddedToOrderEvent->identifier);
        $this->assertSame($this->product->id, $productAddedToOrderEvent->data['productId']);
        $this->assertSame(1, $productAddedToOrderEvent->data['quantity']);
        $this->assertSame($this->product->price, actual: $productAddedToOrderEvent->data['price']);

        $this->sut->addProductItem($this->product);

        $items = $this->sut->listProductItems();

        $this->assertArrayHasKey($this->product->id, $items);
        $this->assertSame(2, $items[$this->product->id]->quantity);
        $this->assertSame($this->product->price, $items[$this->product->id]->price);

        $events = $this->sut->getEvents();
        $this->assertNotEmpty($events);

        $productAddedToOrderEvent = array_shift($events);
        $this->sut->commitEvent($productAddedToOrderEvent);

        $this->assertInstanceOf(ProductItemAddedToOrder::class, $productAddedToOrderEvent);
        $this->assertSame($this->orderId, $productAddedToOrderEvent->identifier);
        $this->assertSame($this->product->id, $productAddedToOrderEvent->data['productId']);
        $this->assertSame(2, $productAddedToOrderEvent->data['quantity']);
        $this->assertSame($this->product->price, actual: $productAddedToOrderEvent->data['price']);
    }

    public function testCanRemoveProductFromOrder(): void
    {
        $this->sut->addProductItem($this->product);
        $this->sut->addProductItem($this->product);
        foreach ($this->sut->getEvents() as $event) {
            $this->sut->commitEvent($event);
        }

        $this->sut->removeProductItem($this->product);

        $items = $this->sut->listProductItems();
        $this->assertArrayHasKey($this->product->id, $items);
        $this->assertSame(1, $items[$this->product->id]->quantity);

        $this->sut->removeProductItem($this->product);

        $items = $this->sut->listProductItems();
        $this->assertArrayNotHasKey($this->product->id, $items);

        $events = $this->sut->getEvents();
        $this->assertNotEmpty($events);

        foreach ($events as $event) {
            $this->sut->commitEvent($event);

            $this->assertInstanceOf(ProductItemRemovedFromOrder::class, $event);
            $this->assertSame($this->orderId, $event->identifier);
            $this->assertSame($this->product->id, $event->data['productId']);
        }
        $this->sut->removeProductItem($this->product);
    }

    public function testCannotMarkOrderAsPlacedIfAlreadyPlaced(): void
    {
        $this->assertFalse($this->sut->isPlaced());

        $this->sut->addProductItem($this->product);
        foreach ($this->sut->getEvents() as $event) {
            $this->sut->commitEvent($event);
        }

        $this->sut->markOrderAsPlaced();

        $this->assertTrue($this->sut->isPlaced());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order is already placed');

        $this->sut->markOrderAsPlaced();
    }

    public function testCannotMarkOrderAsPlacedIfNoItems(): void
    {
        $this->assertFalse($this->sut->isPlaced());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order must have at least one item to be placed');

        $this->sut->markOrderAsPlaced();

        $this->assertFalse($this->sut->isPlaced());
    }

    public function testMarkOrderAsPlaced(): void
    {
        $this->assertFalse($this->sut->isPlaced());

        $this->sut->addProductItem($this->product);
        foreach ($this->sut->getEvents() as $event) {
            $this->sut->commitEvent($event);
        }

        $this->sut->markOrderAsPlaced();

        $this->assertTrue($this->sut->isPlaced());

        $events = $this->sut->getEvents();
        $this->assertNotEmpty($events);

        foreach ($events as $event) {
            $this->sut->commitEvent($event);
            $this->assertInstanceOf(OrderPlaced::class, $event);
            $this->assertSame($this->orderId, $event->identifier);
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order is already placed');
        $this->sut->markOrderAsPlaced();
    }

    public function testCanCalculateTotalPrice(): void
    {
        $this->assertSame(0.0, $this->sut->calculateTotalPrice());
        $this->sut->addProductItem($this->product);
        $this->assertSame(10.0, $this->sut->calculateTotalPrice());
        $this->sut->addProductItem($this->product);
        $this->assertSame(20.0, $this->sut->calculateTotalPrice());
        $this->sut->removeProductItem($this->product);
        $this->assertSame(10.0, $this->sut->calculateTotalPrice());
        $this->sut->removeProductItem($this->product);
        $this->assertSame(0.0, $this->sut->calculateTotalPrice());
        $this->assertNotSame(10.0, $this->sut->calculateTotalPrice());
    }
}
