<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Commands\CreateOrder as CreateOrderCommand;
use App\Application\Handlers\CreateOrder as CreateOrderHandler;
use App\Domain\Order;
use App\Domain\{OrderFactory, OrderRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\IDispatcher;
use Frete\Core\Domain\Errors\FactoryError;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateOrderTest extends TestCase
{
    private OrderFactory|MockObject $orderFactoryMock;
    private OrderRepository|MockObject $orderRepositoryMock;
    private IDispatcher|MockObject $dispatcherMock;
    private CreateOrderHandler $sut;
    private string $orderId;
    private CreateOrderCommand $command;
    private Order $order;

    protected function setUp(): void
    {
        $this->orderFactoryMock = $this->createMock(OrderFactory::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepository::class);
        $this->dispatcherMock = $this->createMock(IDispatcher::class);

        $this->sut = new CreateOrderHandler(
            $this->orderFactoryMock,
            $this->orderRepositoryMock,
            $this->dispatcherMock
        );

        $this->command = $this->createStub(CreateOrderCommand::class);
        $this->order = $this->createStub(Order::class);
    }

    /**
     * @test
     */
    public function handleResultAsSuccessWithOrder()
    {
        $this->orderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->command)
            ->willReturn($this->order);
        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->order)
            ->willReturn($this->order);
        $this->dispatcherMock
            ->expects($this->once())
            ->method('dispatchContextEvents')
            ->with($this->order);

        $result = $this->sut->handle($this->command);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals($this->order, $result->getValue());
    }

    /**
     * @test
     */
    public function handleResultAsFailureWithApplicationError()
    {
        $this->orderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->command)
            ->willReturn(null);
        $this->orderRepositoryMock
            ->expects($this->never())
            ->method('save');
        $this->dispatcherMock
            ->expects($this->never())
            ->method('dispatchContextEvents');

        $result = $this->sut->handle($this->command);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(FactoryError::class, $result->getError());
    }

    /**
     * @test
     */
    public function handleResultAsFailureWithRepositoryError(): void
    {
        $this->orderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->command)
            ->willReturn($this->order);
        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->order)
            ->willReturn(new RepositoryError('error'));
        $this->dispatcherMock
            ->expects($this->never())
            ->method('dispatchContextEvents');

        $result = $this->sut->handle($this->command);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $result->getError());
    }
}
