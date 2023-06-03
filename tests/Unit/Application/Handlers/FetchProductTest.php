<?php

declare(strict_types=1);

use App\Application\Handlers\FetchProduct as Handler;
use App\Application\Queries\{FetchProduct, FetchProduct as Query};
use App\Domain\{Product, ProductRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FetchProductTest extends TestCase
{
    private ProductRepository|MockObject $productRepositoryMock;
    private Handler $sut;
    private Product|MockObject $product;

    protected function setUp(): void
    {
        $this->product = $this->createStub(Product::class);
        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->sut = new Handler($this->productRepositoryMock);
    }

    /**
     * @test
     */
    public function handleResultAsSuccessWithProduct()
    {
        $query = new FetchProduct('123');
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with($query->productId)
            ->willReturn($this->product);

        $result = $this->sut->handle($query);

        $this->assertTrue($result->isSuccess());
        $this->assertEquals($this->product, $result->getValue());
    }

    /**
     * @test
     */
    public function handleResultAsFailureWithRepositoryError()
    {
        $query = new FetchProduct('123');
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with($query->productId)
            ->willReturn(new RepositoryError('Error'));

        $result = $this->sut->handle($query);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(RepositoryError::class, $result->getError());
    }

    /**
     * @test
     */
    public function handleResultAsFailureWithInvalidQueryType()
    {
        $newQuery = new class() extends Frete\Core\Application\Query {
        };
        $query = $newQuery;

        $result = $this->sut->handle($query);

        $this->assertTrue($result->isFailure());
        $this->assertInstanceOf(ApplicationError::class, $result->getError());
    }
}
