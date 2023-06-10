<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Commands\AddProductToOrder;
use App\Application\Handlers\AddProductToOrder as AddProductToOrderHandler;
use App\Domain\{Order, Product};
use App\Domain\{OrderRepository, ProductRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{Command, IDispatcher};
use Frete\Core\Domain\Errors\DomainError;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddProductToOrderHandlerTest extends TestCase
{
    private ProductRepository|MockObject $productRepository;
    private OrderRepository|MockObject $orderRepository;
    private IDispatcher|MockObject $dispatcher;
    private AddProductToOrderHandler $handler;
    private AddProductToOrder|MockObject $command;
    private Order|MockObject $order;
    private Product|MockObject $product;

    protected function setUp(): void
    {
        $this->product = $this->createMock(Product::class);
        $this->order = $this->createMock(Order::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->dispatcher = $this->createMock(IDispatcher::class);
        $this->command = $this->getMockBuilder(AddProductToOrder::class)
            ->setConstructorArgs([1, 1])
            ->getMock();
        $this->handler = new AddProductToOrderHandler(
            $this->productRepository,
            $this->orderRepository,
            $this->dispatcher
        );
    }

    /**
     * @test
     */
    public function handleWithInvalidCommandShouldReturnFailureResult()
    {
        $this->productRepository->expects($this->never())->method('get');
        $this->orderRepository->expects($this->never())->method('get');
        $this->order->expects($this->never())->method('addProductItem');
        $this->orderRepository->expects($this->never())->method('save');
        $this->dispatcher->expects($this->never())->method('dispatchContextEvents');

        $this->command = $this->createMock(Command::class);

        $result = $this->handler->handle($this->command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(ApplicationError::class, $error);
        $this->assertStringContainsString('Invalid command type', $error->getMessage());
    }

    /**
     * @test
     */
    public function handleWhenProductRepositoryErrorShouldReturnFailureResult()
    {
        $this->productRepository->expects($this->once())->method('get')->willReturn(new RepositoryError('Not found'));
        $this->orderRepository->expects($this->never())->method('get');
        $this->order->expects($this->never())->method('addProductItem');
        $this->orderRepository->expects($this->never())->method('save');
        $this->dispatcher->expects($this->never())->method('dispatchContextEvents');

        $result = $this->handler->handle($this->command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenOrderRepositoryErrorShouldReturnFailureResult()
    {
        $this->productRepository->expects($this->once())->method('get')->willReturn($this->product);
        $this->orderRepository->expects($this->once())->method('get')->willReturn(new RepositoryError('Not found'));
        $this->order->expects($this->never())->method('addProductItem');
        $this->orderRepository->expects($this->never())->method('save');
        $this->dispatcher->expects($this->never())->method('dispatchContextEvents');

        $result = $this->handler->handle($this->command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenOrderDomainErrorShouldReturnFailureResult()
    {
        $this->productRepository->expects($this->once())
            ->method('get')
            ->with($this->command->productId)
            ->willReturn($this->product);

        $this->orderRepository->expects($this->once())->method('get')
            ->with($this->command->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('addProductItem')
            ->with($this->product)
            ->willReturn(new DomainError('Error', 1));

        $this->orderRepository->expects($this->never())->method('save');

        $this->dispatcher->expects($this->never())->method('dispatchContextEvents');

        $result = $this->handler->handle($this->command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(DomainError::class, $error);
    }

    /**
     * @test
     */
    public function handleWhenSaveOrderRepositoryErrorShouldReturnFailureResult()
    {
        $this->productRepository->expects($this->once())
            ->method('get')->with($this->command->productId)
            ->willReturn($this->product);

        $this->orderRepository->expects($this->once())
            ->method('get')->with($this->command->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('addProductItem')->with($this->product)
            ->willReturn(true);

        $this->orderRepository->expects($this->once())
            ->method('save')->with($this->order)
            ->willReturn(new RepositoryError('Error'));

        $this->dispatcher->expects($this->never())->method('dispatchContextEvents');

        $result = $this->handler->handle($this->command);
        $error = $result->getError();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $error);
    }

    /**
     * @test
     */
    public function handleWithValidScenarioShouldReturnSuccessResult()
    {
        $this->productRepository->expects($this->once())
            ->method('get')->with($this->command->productId)
            ->willReturn($this->product);

        $this->orderRepository->expects($this->once())
            ->method('get')->with($this->command->orderId)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('addProductItem')->with($this->product)
            ->willReturn(true);

        $this->orderRepository->expects($this->once())
            ->method('save')->with($this->order)
            ->willReturn($this->order);

        $this->dispatcher->expects($this->once())
            ->method('dispatchContextEvents')->with($this->order);

        $result = $this->handler->handle($this->command);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertInstanceOf(Order::class, $result->getvalue());
    }
}
