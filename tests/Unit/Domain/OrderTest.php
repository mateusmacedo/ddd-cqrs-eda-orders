<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Events\{OrderInitialized, ProductItemAddedToOrder, ProductItemRemovedFromOrder};
use App\Domain\{Order, Product};
use App\Domain\Events\OrderPlaced;
use ArrayObject;
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
        $this->sut = Order::init($this->orderId);
        $events = $this->sut->getEvents();
        $orderInitializedEvent = array_shift($events);
        $this->sut->commitEvent($orderInitializedEvent);

        $this->product = new Product(...[
            'id' => '123',
            'name' => 'Product 1',
            'description' => 'Product 1 description',
            'price' => 10.00,
            'createdAt' => date('Y-m-d H:i:s')
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->orderId, $this->items, $this->sut, $this->product);
    }

    public function testOrderCanBeInitialized(): void
    {
        $order = Order::init($this->orderId);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($this->orderId, $order->id);
        $this->assertInstanceOf(ArrayObject::class, $order->items);
        $this->assertNotNull($order->createdAt);
        $this->assertNotEmpty($order->getEvents());
        $this->assertTrue($order->isInitialized());

        $events = $order->getEvents();
        $orderInitializedEvent = array_shift($events);

        $this->assertInstanceOf(OrderInitialized::class, $orderInitializedEvent);
        $this->assertSame($this->orderId, $orderInitializedEvent->identifier);
        $this->assertSame([], $orderInitializedEvent->data['items']);
        $this->assertSame($order->createdAt, $orderInitializedEvent->data['createdAt']);
    }

    public function testCanAddProductOnOrder(): void
    {
        $this->sut->addProductItem($this->product);

        $this->assertNotEmpty($this->sut->items);

        $items = $this->sut->items->getArrayCopy();

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

        $items = $this->sut->items->getArrayCopy();

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
        foreach($this->sut->getEvents() as $event) {
            $this->sut->commitEvent($event);
        }

        $this->sut->removeProductItem($this->product);

        $items = $this->sut->items->getArrayCopy();
        $this->assertArrayHasKey($this->product->id, $items);
        $this->assertSame(1, $items[$this->product->id]->quantity);

        $this->sut->removeProductItem($this->product);

        $items = $this->sut->items->getArrayCopy();
        $this->assertArrayNotHasKey($this->product->id, $items);

        $events = $this->sut->getEvents();
        $this->assertNotEmpty($events);

        foreach($events as $event) {
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
        foreach($this->sut->getEvents() as $event) {
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
        foreach($this->sut->getEvents() as $event) {
            $this->sut->commitEvent($event);
        }

        $this->sut->markOrderAsPlaced();

        $this->assertTrue($this->sut->isPlaced());

        $events = $this->sut->getEvents();
        $this->assertNotEmpty($events);

        foreach($events as $event) {
            $this->sut->commitEvent($event);
            $this->assertInstanceOf(OrderPlaced::class, $event);
            $this->assertSame($this->orderId, $event->identifier);
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order is already placed');
        $this->sut->markOrderAsPlaced();
    }
}
