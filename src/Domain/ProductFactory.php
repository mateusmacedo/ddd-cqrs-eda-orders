<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\Commands\RegisterProduct;
use App\Domain\Events\ProductRegistered;
use Frete\Core\Domain\AbstractFactory;

class ProductFactory extends AbstractFactory
{
    /**
     * @param RegisterProduct $data
     * @param string          $id
     *
     * @return Product
     */
    public function create(mixed $data = null, mixed $id = null): mixed
    {
        $this->reset($data);

        $this->item->addEvent(new ProductRegistered($this->item->id, [
            'name' => $this->item->name,
            'description' => $this->item->description,
            'price' => $this->item->price,
            'createdAt' => $this->item->createdAt->format('Y-m-d H:i:s'),
        ]));

        return $this->item;
    }

    /**
     * @param RegisterProduct $data
     */
    protected function reset(mixed $data): void
    {
        $this->item = new Product(
            $data->productId,
            $data->name,
            $data->description,
            $data->price
        );
    }
}
