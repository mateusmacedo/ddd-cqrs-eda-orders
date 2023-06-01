<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Events\OrderInitialized;
use App\Domain\Order;
use ArrayObject;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private string $orderId;
    private ArrayObject $items;

    protected function setUp(): void
    {
        $this->orderId = '123';
        $this->items = new ArrayObject();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->orderId, $this->items);
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
}
