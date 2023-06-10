<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\RegisterProduct as RegisterProductCommand;
use App\Domain\{Product, ProductRepository};
use Frete\Core\Application\{IDispatcher, IHandler};
use Frete\Core\Domain\{AggregateRoot, IEventStore, Message};
use Frete\Core\Domain\AbstractFactory;
use Frete\Core\Domain\Errors\FactoryError;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class RegisterProduct implements IHandler
{
    public function __construct(
        private AbstractFactory $factory,
        private ProductRepository $repository,
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
        /** @var null|Product */
        $product = $this->factory->create($command);

        if (!$product) {
            return Result::failure(new FactoryError('Product cannot be created'));
        }

        $result = $this->repository->save($product);

        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        $this->dispatcher->dispatchContextEvents($product);

        return Result::success($product);
    }
}
