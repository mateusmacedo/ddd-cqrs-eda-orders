<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Commands\RemoveProductFromOrder as RemoveProductFromOrderCommand;
use App\Application\Handlers\RemoveProductFromOrder;
use App\Domain\{Order, OrderRepository, Product, ProductRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{Command, IDispatcher};
use Frete\Core\Domain\Errors\DomainError;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RemoveProductFromOrderTest extends TestCase
{
    private OrderRepository|MockObject $orderRepositoryMock;
    private ProductRepository|MockObject $productRepositoryMock;
    private IDispatcher|MockObject $dispatcherMock;
    private Order|MockObject $orderOrError;
    private Product|MockObject $productOrError;
    private RemoveProductFromOrder $handler;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepository::class);
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->dispatcherMock = $this->createMock(IDispatcher::class);

        $this->handler = new RemoveProductFromOrder(
            $this->orderRepositoryMock,
            $this->productRepositoryMock,
            $this->dispatcherMock
        );

        $this->orderOrError = $this->createMock(Order::class);
        $this->productOrError = $this->createMock(Product::class);
    }

    /**
     * @test
     */
    public function handleWhenIsInvalidCommandShouldReturnResultIsFailure()
    {
        $command = $this->createMock(Command::class);
        $result = $this->handler->handle($command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(ApplicationError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenOrderRepositoryGetReturnRepositoryErrorShouldReturnResultIsFailure()
    {
        $command = $this->getMockBuilder(RemoveProductFromOrderCommand::class)
            ->setConstructorArgs(['1', '1'])
            ->getMock();
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn(new RepositoryError('Error'));

        $result = $this->handler->handle($command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenProductRepositoryGetReturnRepositoryErrorShouldReturnResultIsFailure()
    {
        $command = $this->getMockBuilder(RemoveProductFromOrderCommand::class)
            ->setConstructorArgs(['1', '1'])
            ->getMock();
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($this->orderOrError);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn(new RepositoryError('Error'));

        $result = $this->handler->handle($command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenOrderOrErrorRemoveProductItemReturnDomainErrorShouldReturnResultIsFailure()
    {
        $command = $this->getMockBuilder(RemoveProductFromOrderCommand::class)
            ->setConstructorArgs(['1', '1'])
            ->getMock();
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($this->orderOrError);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($this->productOrError);
        $this->orderOrError->expects($this->once())
            ->method('removeProductItem')
            ->willReturn(new DomainError('Error', 1));

        $result = $this->handler->handle($command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(DomainError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenOrderRepositorySaveReturnRepositoryErrorShouldReturnResultIsFailure()
    {
        $command = $this->getMockBuilder(RemoveProductFromOrderCommand::class)
            ->setConstructorArgs(['1', '1'])
            ->getMock();
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($this->orderOrError);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($this->productOrError);
        $this->orderOrError->expects($this->once())
            ->method('removeProductItem')
            ->willReturn(true);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->willReturn(new RepositoryError('Error'));

        $result = $this->handler->handle($command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function handleWithIsValidScenario(): void
    {
        $command = $this->getMockBuilder(RemoveProductFromOrderCommand::class)
            ->setConstructorArgs(['1', '1'])
            ->getMock();
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($command->orderId)
            ->willReturn($this->orderOrError);
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($command->productId)
            ->willReturn($this->productOrError);
        $this->orderOrError->expects($this->once())
            ->method('removeProductItem')
            ->with($this->productOrError)
            ->willReturn(true);
        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderOrError)
            ->willReturn($this->orderOrError);
        $this->dispatcherMock->expects($this->once())
            ->method('dispatchContextEvents')
            ->with($this->orderOrError);

        $result = $this->handler->handle($command);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame($this->orderOrError, $result->getValue());
    }
}
