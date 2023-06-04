<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\RemoveProductFromOrder as RemoveProductFromOrderCommand;
use App\Domain\{OrderRepository, ProductRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\Errors\DomainError;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class RemoveProductFromOrder implements IHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ProductRepository $productRepository,
        private IDispatcher $dispatcher
    ) {
    }

    /**
     * @param RemoveProductFromOrderCommand $command
     *
     * @return Result
     */
    public function handle(Message $command): Result
    {
        if (!$command instanceof RemoveProductFromOrderCommand) {
            return Result::failure(new ApplicationError('Invalid command'));
        }

        $orderOrError = $this->orderRepository->get($command->orderId);
        if ($orderOrError instanceof RepositoryError) {
            return Result::failure($orderOrError);
        }

        $productOrError = $this->productRepository->get($command->productId);
        if ($productOrError instanceof RepositoryError) {
            return Result::failure($productOrError);
        }

        $result = $orderOrError->removeProductItem($productOrError);
        if ($result instanceof DomainError) {
            return Result::failure($result);
        }

        $orderOrError = $this->orderRepository->save($orderOrError);
        if ($orderOrError instanceof RepositoryError) {
            return Result::failure($orderOrError);
        }

        $this->dispatcher->dispatchContextEvents($orderOrError);

        return Result::success($orderOrError);
    }
}
