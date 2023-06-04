<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\RegisterProduct as RegisterProductCommand;
use App\Domain\ProductFactory;
use App\Domain\{Product, ProductRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\{AggregateRoot, IEventStore, Message};
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class RegisterProduct implements IHandler
{
    public function __construct(
        private ProductFactory $productFactory,
        private ProductRepository $productRepository,
        private IDispatcher $dispatcher
    ) {
    }

    /**
     * @param RegisterProductCommand $command
     *
     * @return Result
     */
    public function handle(Message $command): Result
    {
        /** @var Product|null */
        $product = $this->productFactory->create($command);

        if (!$product) {
            return Result::failure(new ApplicationError('Product cannot be created'));
        }

        $result = $this->productRepository->save($product);

        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        $this->dispatcher->dispatchContextEvents($product);

        return Result::success($product);
    }
}
