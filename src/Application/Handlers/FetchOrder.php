<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\FetchOrder as FetchOrderQuery;
use App\Domain\OrderRepository;
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\IHandler;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class FetchOrder implements IHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository
    ) {
    }

    /**
     * @param FetchOrderQuery $query
     *
     * @return Result
     */
    public function handle(Message $query): Result
    {
        if (!$query instanceof FetchOrderQuery) {
            return Result::failure(new ApplicationError('Invalid query type'));
        }

        $result = $this->orderRepository->get($query->orderId);

        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        return Result::success($result);
    }
}
