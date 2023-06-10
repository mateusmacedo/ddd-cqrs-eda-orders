<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers;

use App\Application\Commands\RegisterProduct as Command;
use App\Application\Handlers\RegisterProduct as Handler;
use App\Domain\ProductFactory;
use App\Domain\{Product, ProductRepository};
use Frete\Core\Application\IDispatcher;
use Frete\Core\Domain\Errors\FactoryError;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisterProductTest extends TestCase
{
    private Command|MockObject $command;
    private Product|MockObject $product;
    private ProductFactory|MockObject $productFactoryMock;
    private ProductRepository|MockObject $productRepositoryMock;
    private IDispatcher|MockObject $dispatcherMock;
    private Handler $sut;

    protected function setUp(): void
    {
        $this->productFactoryMock = $this->createMock(ProductFactory::class);
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->dispatcherMock = $this->createMock(IDispatcher::class);

        $this->sut = new Handler(
            $this->productFactoryMock,
            $this->productRepositoryMock,
            $this->dispatcherMock
        );

        $this->command = $this->createStub(Command::class);

        $this->product = $this->createStub(Product::class);
    }

    /**
     * @test
     */
    public function handleResultAsSuccessWithProduct()
    {
        $this->productFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->command)
            ->willReturn($this->product);
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->product)
            ->willReturn($this->product);
        $this->dispatcherMock
            ->expects($this->once())
            ->method('dispatchContextEvents')
            ->with($this->product);

        $result = $this->sut->handle($this->command);

        $this->assertTrue($result->isSuccess());
        $this->assertSame($this->product, $result->getValue());
    }

    /**
     * @test
     */
    public function handleResultAsFailureWithApplicationError()
    {
        $this->productFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->command)
            ->willReturn(null);

        $result = $this->sut->handle($this->command);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(FactoryError::class, $result->getError());
    }

    /**
     * @test
     */
    public function handleResultAsFailureWithRepositoryError(): void
    {
        $this->productFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->command)
            ->willReturn($this->product);
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->product)
            ->willReturn(new RepositoryError('error'));

        $result = $this->sut->handle($this->command);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $result->getError());
    }
}
