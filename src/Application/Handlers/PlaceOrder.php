<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\PlaceOrder as PlaceOrderCommand;
use App\Domain\OrderRepository;
use DomainException;
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class PlaceOrder implements IHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
        private IDispatcher $dispatcher
    ) {
    }

    /**
     * @param PlaceOrderCommand $command
     *
     * @return Result
     */
    public function handle(Message $command): Result
    {
        if (!$command instanceof PlaceOrderCommand) {
            return Result::failure(new ApplicationError('Invalid command'));
        }

        $orderOrError = $this->orderRepository->get($command->orderId);
        if ($orderOrError instanceof RepositoryError) {
            return Result::failure($orderOrError);
        }

        try {
            $orderOrError->markOrderAsPlaced();
        } catch (DomainException $e) {
            return Result::failure($e);
        }

        $result = $this->orderRepository->save($orderOrError);
        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        $this->dispatcher->dispatchContextEvents($result);

        return Result::success($result);
    }
}
