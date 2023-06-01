<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Events\OrderInitialized;
use Frete\Core\Domain\AbstractFactory;

class OrderFactory extends AbstractFactory
{
    /**
     * @param mixed $data
     * @param mixed $id
     *
     * @return Order
     */
    public function create(mixed $data = null, mixed $id = null): mixed
    {
        $this->reset($id);
        $this->item->addEvent(new OrderInitialized($id, [
            'items' => $this->item->listProductItems(),
            'createdAt' => $this->item->createdAt->format('Y-m-d H:i:s'),
        ]));

        return $this->item;
    }

    /**
     * @param string $data
     */
    protected function reset(mixed $data): void
    {
        $this->item = new Order($data);
    }
}
