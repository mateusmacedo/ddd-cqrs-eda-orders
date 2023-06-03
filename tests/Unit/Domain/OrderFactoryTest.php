<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Application\Commands\CreateOrder;
use App\Domain\Events\OrderCreated;
use App\Domain\OrderFactory;
use App\Domain\{Order, Product};
use PHPUnit\Framework\TestCase;

class OrderFactoryTest extends TestCase
{
    private string $orderId;
    private OrderFactory $sut;
    private CreateOrder $command;

    protected function setUp(): void
    {
        $this->orderId = '123';
        $this->command = new CreateOrder(
            $this->orderId,
        );
        $this->sut = new OrderFactory();
    }

    /**
     * @test
     */
    public function order_can_be_created(): void
    {
        $order = $this->sut->create($this->command);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($this->orderId, $order->id);
        $this->assertSame([], $order->listProductItems());
        $this->assertNotNull($order->createdAt);
        $this->assertNotEmpty($order->getEvents());
        $this->assertTrue($order->isInitialized());

        $events = $order->getEvents();
        $orderInitializedEvent = array_shift($events);

        $this->assertInstanceOf(OrderCreated::class, $orderInitializedEvent);
        $this->assertSame($this->orderId, $orderInitializedEvent->identifier);
        $this->assertSame([], $orderInitializedEvent->data['items']);
        $this->assertSame($order->createdAt->format('Y-m-d H:i:s'), $orderInitializedEvent->data['createdAt']);
    }

    /**
     * @test
     */
    public function order_cannot_be_created_with_invalid_data(): void
    {
        $this->assertNull($this->sut->create(null));
        $this->assertNull($this->sut->create(new \stdClass()));
        $this->assertNull($this->sut->create([]));
        $this->assertNull($this->sut->create(''));
        $this->assertNull($this->sut->create(1));
        $this->assertNull($this->sut->create(1.0));
        $this->assertNull($this->sut->create(true));
    }
}
