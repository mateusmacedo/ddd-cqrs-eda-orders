<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Handlers\FetchOrder;
use App\Application\Queries\FetchOrder as FetchOrderQuery;
use App\Domain\Order;
use App\Domain\OrderRepository;
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use PHPUnit\Framework\TestCase;

class FetchOrderTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsOrderOnValidQuery()
    {
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->method('get')
            ->willReturn($this->createStub(Order::class));

        $handler = new FetchOrder($orderRepository);

        $query = new FetchOrderQuery('abc');
        $result = $handler->handle($query);

        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(Order::class, $result->getValue());
    }

    /**
     * @test
     */
    public function itReturnsFailureOnRepositoryError()
    {
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->method('get')
            ->willReturn(new RepositoryError('Database connection failed'));

        $handler = new FetchOrder($orderRepository);

        $query = new FetchOrderQuery('abc');
        $result = $handler->handle($query);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $result->getError());
    }

    /**
     * @test
     */
    public function itReturnsFailureOnInvalidQueryType()
    {
        $orderRepository = $this->createMock(OrderRepository::class);

        $handler = new FetchOrder($orderRepository);

        $query = $this->createMock(Message::class);
        $result = $handler->handle($query);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(ApplicationError::class, $result->getError());
    }
}
