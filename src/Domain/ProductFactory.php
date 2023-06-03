<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\Commands\RegisterProduct;
use App\Domain\Events\ProductRegistered;
use Frete\Core\Domain\AbstractFactory;

class ProductFactory extends AbstractFactory
{
    /**
     * @var Product
     */
    protected object $item;

    public function create(mixed $data = null, mixed $id = null): mixed
    {
        if ($data instanceof RegisterProduct) {
            $this->reset($data);

            $this->item->addEvent(new ProductRegistered($this->item->id, [
                'name' => $this->item->name,
                'description' => $this->item->description,
                'price' => $this->item->price,
                'createdAt' => $this->item->createdAt->format('Y-m-d H:i:s'),
            ]));

            return $this->item;
        }

        return null;
    }

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
