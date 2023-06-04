<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Handlers\CalculateOrder as CalculateOrderHandler;
use App\Application\Queries\CalculateOrder as CalculateOrderQuery;
use App\Domain\{Order, OrderRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\Query;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CalculateOrderTest extends TestCase
{
    private OrderRepository|MockObject $orderRepository;
    private CalculateOrderHandler $handler;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->handler = new CalculateOrderHandler($this->orderRepository);
    }

    /**
     * @test
     */
    public function shouldReturnErrorWhenQueryIsInvalid(): void
    {
        $query = $this->createMock(originalClassName: Query::class);

        $result = $this->handler->handle($query);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(ApplicationError::class, $error);
    }

    /**
     * @test
     */
    public function shouldReturnErrorWhenOrderRepositoryFails(): void
    {
        $query = $this->getMockBuilder(CalculateOrderQuery::class)
            ->setConstructorArgs(['1'])
            ->getMock();

        $this->orderRepository->expects($this->once())
            ->method('get')->with($query->orderId)
            ->willReturn(new RepositoryError('Error'));

        $result = $this->handler->handle($query);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function shouldReturnSuccessWhenOrderRepositorySucceeds(): void
    {
        $query = $this->getMockBuilder(CalculateOrderQuery::class)
            ->setConstructorArgs(['1'])
            ->getMock();
        $order = $this->createMock(Order::class);
        $order->expects($this->once())
            ->method('calculateTotalPrice')
            ->willReturn(10.0);

        $this->orderRepository->expects($this->once())
            ->method('get')->with($query->orderId)
            ->willReturn($order);

        $result = $this->handler->handle($query);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(10.0, $result->getValue());
    }
}
