<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Application\Commands\CreateOrder;
use App\Domain\Events\OrderCreated;
use App\Domain\OrderFactory;
use App\Domain\{Order, Product};
use PHPUnit\Framework\TestCase;
use stdClass;

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
    public function orderCanBeCreated(): void
    {
        $order = $this->sut->create($this->command);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($this->orderId, $order->id);
        $this->assertSame([], $order->listProductItems());
        $this->assertNotNull($order->initializedAt);
        $this->assertNotEmpty($order->getEvents());
        $this->assertTrue($order->isInitialized());

        $events = $order->getEvents();
        $orderInitializedEvent = array_shift($events);

        $this->assertInstanceOf(OrderCreated::class, $orderInitializedEvent);
        $this->assertSame($this->orderId, $orderInitializedEvent->identifier);
        $this->assertSame([], $orderInitializedEvent->data['items']);
        $this->assertSame($order->initializedAt->format('Y-m-d H:i:s'), $orderInitializedEvent->data['initializedAt']);
    }

    /**
     * @test
     */
    public function orderCannotBeCreatedWithInvalidData(): void
    {
        $this->assertNull($this->sut->create(null));
        $this->assertNull($this->sut->create(new stdClass()));
        $this->assertNull($this->sut->create([]));
        $this->assertNull($this->sut->create(''));
        $this->assertNull($this->sut->create(1));
        $this->assertNull($this->sut->create(1.0));
        $this->assertNull($this->sut->create(true));
    }
}
