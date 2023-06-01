<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Events\ProductRegistered;
use App\Domain\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    private string $productId;
    private string $productName;
    private string $productDescription;
    private float $productPrice;

    protected function setUp(): void
    {
        $this->productId = '123';
        $this->productName = 'Test Product';
        $this->productDescription = 'Test description';
        $this->productPrice = 10.0;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productId, $this->productName, $this->productDescription, $this->productPrice);
    }

    public function testProductCanBeRegistered(): void
    {
        $product = Product::register(
            $this->productId,
            $this->productName,
            $this->productDescription,
            $this->productPrice
        );

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($product->id, $this->productId);
        $this->assertEquals($product->name, $this->productName);
        $this->assertEquals($product->description, $this->productDescription);
        $this->assertEquals($product->price, $this->productPrice);
    }

    public function testProductRegisteredEventIsAdded(): void
    {
        $product = Product::register(
            $this->productId,
            $this->productName,
            $this->productDescription,
            $this->productPrice,
        );

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
        $this->assertEquals($eventData['createdAt'], $product->createdAt);
    }
}
