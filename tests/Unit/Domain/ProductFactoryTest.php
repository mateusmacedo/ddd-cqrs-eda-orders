<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Application\Commands\RegisterProduct;
use App\Domain\Events\ProductRegistered;
use App\Domain\{Product, ProductFactory};
use PHPUnit\Framework\TestCase;

final class ProductFactoryTest extends TestCase
{
    private string $productId;
    private string $productName;
    private string $productDescription;
    private float $productPrice;
    private RegisterProduct $registerProduct;
    private ProductFactory $sut;

    protected function setUp(): void
    {
        $this->productId = '123';
        $this->productName = 'Test Product';
        $this->productDescription = 'Test description';
        $this->productPrice = 10.0;
        $this->registerProduct = new RegisterProduct(
            $this->productId,
            $this->productName,
            $this->productDescription,
            $this->productPrice
        );
        $this->sut = new ProductFactory();
    }

    public function testProductCanBeCreated(): void
    {
        $product = $this->sut->create($this->registerProduct);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($product->id, $this->productId);
        $this->assertEquals($product->name, $this->productName);
        $this->assertEquals($product->description, $this->productDescription);
        $this->assertEquals($product->price, $this->productPrice);

        $events = $product->getEvents();

        $this->assertNotEmpty($events);

        $productRegisteredEvent = array_shift($events);

        $this->assertInstanceOf(ProductRegistered::class, $productRegisteredEvent);

        $eventData = $productRegisteredEvent->data;

        $this->assertArrayHasKey('name', $eventData);
        $this->assertEquals($eventData['name'], $this->productName);

        $this->assertArrayHasKey('description', $eventData);
        $this->assertEquals($eventData['description'], $this->productDescription);

        $this->assertArrayHasKey('price', $eventData);
        $this->assertEquals($eventData['price'], $this->productPrice);

        $this->assertArrayHasKey('createdAt', $eventData);
        $this->assertEquals($eventData['createdAt'], $product->createdAt->format('Y-m-d H:i:s'));
    }
}
