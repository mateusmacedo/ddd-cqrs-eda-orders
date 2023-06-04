<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Commands\PlaceOrder as PlaceOrderCommand;
use App\Application\Handlers\PlaceOrder as PlaceOrderHandler;
use App\Domain\{Order, OrderRepository};
use DomainException;
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{Command, IDispatcher};
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PlaceOrderTest extends TestCase
{
    private OrderRepository|MockObject $orderRepository;
    private IDispatcher|MockObject $dispatcher;
    private PlaceOrderHandler $handler;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->dispatcher = $this->createMock(IDispatcher::class);
        $this->handler = new PlaceOrderHandler($this->orderRepository, $this->dispatcher);
    }

    /**
     * @test
     */
    public function shouldReturnFailureWhenCommandIsNotValid(): void
    {
        $command = $this->createStub(Command::class);

        $result = $this->handler->handle($command);
        $error = $result->getError();

        self::assertTrue($result->isFailure());
        self::assertInstanceOf(ApplicationError::class, $error);
    }

    /**
     * @test
     */
    public function shouldReturnFailureWhenOrderRepositoryFails(): void
    {
        $command = $this->getMockBuilder(PlaceOrderCommand::class)
            ->setConstructorArgs(['1'])
            ->getMock();
        $this->orderRepository->method('get')->willReturn(new RepositoryError('Error'));

        $result = $this->handler->handle($command);
        $error = $result->getError();

        self::assertTrue($result->isFailure());
        self::assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function shouldReturnFailureWhenOrderIsNotPlaced(): void
    {
        $command = $this->getMockBuilder(PlaceOrderCommand::class)
            ->setConstructorArgs(['1'])
            ->getMock();
        $order = $this->createStub(Order::class);
        $order->method('markOrderAsPlaced')->willThrowException(new DomainException('Error'));
        $this->orderRepository->method('get')->willReturn($order);

        $result = $this->handler->handle($command);
        $error = $result->getError();

        self::assertTrue($result->isFailure());
        self::assertInstanceOf(DomainException::class, $error);
    }

    /**
     * @test
     */
    public function shouldReturnFailureWhenOrderRepositoryFailsToSave(): void
    {
        $command = $this->getMockBuilder(PlaceOrderCommand::class)
            ->setConstructorArgs(['1'])
            ->getMock();
        $order = $this->createStub(Order::class);
        $this->orderRepository->method('get')->willReturn($order);
        $this->orderRepository->method('save')->willReturn(new RepositoryError('Error'));

        $result = $this->handler->handle($command);
        $error = $result->getError();

        self::assertTrue($result->isFailure());
        self::assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function shouldReturnSuccessWhenOrderIsPlaced(): void
    {
        $command = $this->getMockBuilder(PlaceOrderCommand::class)
            ->setConstructorArgs(['1'])
            ->getMock();
        $order = $this->createStub(Order::class);
        $this->orderRepository->method('get')->willReturn($order);
        $this->orderRepository->method('save')->willReturn($order);
        $this->dispatcher->expects(self::once())->method('dispatchContextEvents');

        $result = $this->handler->handle($command);

        self::assertTrue($result->isSuccess());
        self::assertInstanceOf(Order::class, $result->getValue());
    }
}
