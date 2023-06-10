<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateOrder as CreateOrderCommand;
use App\Domain\{Order, OrderRepository};
use Frete\Core\Domain\Errors\FactoryError;
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\AbstractFactory;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class CreateOrder implements IHandler
{
    public function __construct(
        private readonly AbstractFactory $orderFactory,
        private readonly OrderRepository $orderRepository,
        private readonly IDispatcher $dispatcher
    ) {
    }

    /**
     * @param CreateOrderCommand $command
     *
     * @return Result
     */
    public function handle(Message $command): Result
    {
        /** @var null|Order */
        $order = $this->orderFactory->create($command);

        if (!$order) {
            return Result::failure(new FactoryError('Order cannot be created'));
        }

        $result = $this->orderRepository->save($order);

        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        $this->dispatcher->dispatchContextEvents($order);

        return Result::success($order);
    }
}
