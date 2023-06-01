<?php

declare(strict_types=1);

namespace Tests\Domain;

use App\Domain\Events\OrderInitialized;
use App\Domain\OrderFactory;
use App\Domain\{Order, Product};
use ArrayObject;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class OrderFactoryTest extends TestCase
{
    private string $orderId;
    private ArrayObject $items;
    private Product $product;
    private OrderFactory $sut;

    protected function setUp(): void
    {
        $this->orderId = '123';
        $this->items = new ArrayObject();
        $this->product = new Product(...[
            'id' => '123',
            'name' => 'Product 1',
            'description' => 'Product 1 description',
            'price' => 10.00,
            'createdAt' => new DateTimeImmutable(),
        ]);
        $this->sut = new OrderFactory();
    }

    public function testOrderCanBeCreated(): void
    {
        $order = $this->sut->create(id: $this->orderId);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($this->orderId, $order->id);
        $this->assertSame([], $order->listProductItems());
        $this->assertNotNull($order->createdAt);
        $this->assertNotEmpty($order->getEvents());
        $this->assertTrue($order->isInitialized());

        $events = $order->getEvents();
        $orderInitializedEvent = array_shift($events);

        $this->assertInstanceOf(OrderInitialized::class, $orderInitializedEvent);
        $this->assertSame($this->orderId, $orderInitializedEvent->identifier);
        $this->assertSame([], $orderInitializedEvent->data['items']);
        $this->assertSame($order->createdAt->format('Y-m-d H:i:s'), $orderInitializedEvent->data['createdAt']);
    }
}
