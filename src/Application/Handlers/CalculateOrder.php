<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\CalculateOrder as CalculateOrderQuery;
use App\Domain\OrderRepository;
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\IHandler;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class CalculateOrder implements IHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {
    }

    /**
     * Undocumented function.
     *
     * @param CalculateOrderQuery $query
     *
     * @return Result
     */
    public function handle(Message $query): Result
    {
        if (!$query instanceof CalculateOrderQuery) {
            return Result::failure(new ApplicationError('Invalid query'));
        }

        $orderOrError = $this->orderRepository->get($query->orderId);
        if ($orderOrError instanceof RepositoryError) {
            return Result::failure($orderOrError);
        }

        return Result::success($orderOrError->calculateTotalPrice());
    }
}
