<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\CreateOrder as CreateOrderCommand;
use App\Domain\{Order, OrderFactory, OrderRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class CreateOrder implements IHandler
{
    public function __construct(
        private readonly OrderFactory $orderFactory,
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
        /** @var Order|null */
        $order = $this->orderFactory->create($command);

        if (!$order) {
            return Result::failure(new ApplicationError('Order cannot be created'));
        }

        $result = $this->orderRepository->save($order);

        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        $this->dispatcher->dispatchContextEvents($order);

        return Result::success($order);
    }
}
