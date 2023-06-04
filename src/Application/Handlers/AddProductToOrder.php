<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\AddProductToOrder as AddProductToOrderCommand;
use App\Domain\{
    OrderRepository,
    ProductRepository
};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\Errors\DomainError;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class AddProductToOrder implements IHandler
{
    public function __construct(
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository,
        private IDispatcher $dispatcher
    ) {
    }

    /**
     * @param AddProductToOrderCommand $command
     *
     * @return Result
     */
    public function handle(Message $command): Result
    {
        if ($command instanceof AddProductToOrderCommand) {
            $productOrError = $this->productRepository->get($command->productId);
            if ($productOrError instanceof RepositoryError) {
                return Result::failure($productOrError);
            }

            $orderOrError = $this->orderRepository->get($command->orderId);
            if ($orderOrError instanceof RepositoryError) {
                return Result::failure($orderOrError);
            }

            $result = $orderOrError->addProductItem($productOrError);
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

        return Result::failure(new ApplicationError('Invalid command type'));
    }
}
