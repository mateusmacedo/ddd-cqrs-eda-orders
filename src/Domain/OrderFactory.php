<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\Commands\CreateOrder;
use App\Domain\Events\OrderCreated;
use Frete\Core\Domain\AbstractFactory;

class OrderFactory extends AbstractFactory
{
    /**
     * @var Order
     */
    protected object $item;

    public function create(mixed $data = null, mixed $id = null): mixed
    {
        if ($data instanceof CreateOrder) {
            $this->reset($data);
            $this->item->addEvent(new OrderCreated($this->item->id, [
                'items' => $this->item->listProductItems(),
                'initializedAt' => $this->item->initializedAt->format('Y-m-d H:i:s'),
            ]));

            return $this->item;
        }

        return null;
    }

    protected function reset(mixed $data): void
    {
        $this->item = new Order($data->orderId);
    }
}
