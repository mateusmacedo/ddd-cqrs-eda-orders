<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Events\OrderInitialized;
use App\Domain\{Order, Product};
use App\Domain\Events\ProductItemAddedToOrder;
use ArrayObject;
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
    }

    public function testOrderInitializedEventIsAdded(): void
    {
        $order = Order::init($this->orderId);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($this->orderId, $order->id);
        $this->assertInstanceOf(ArrayObject::class, $order->items);
        $this->assertNotNull($order->createdAt);
        $this->assertNotEmpty($order->getEvents());

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
}
