<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Queries\FetchProduct as FetchProductQuery;
use App\Domain\{Product, ProductRepository};
use Frete\Core\Application\Errors\ApplicationError;
use Frete\Core\Application\IHandler;
use Frete\Core\Domain\Message;
use Frete\Core\Infrastructure\Database\Errors\RepositoryError;
use Frete\Core\Shared\Result;

class FetchProduct implements IHandler
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    /**
     * @param FetchProductQuery $query
     *
     * @return Result
     */
    public function handle(Message $query): Result
    {
        if (!$query instanceof FetchProductQuery) {
            return Result::failure(new ApplicationError('Invalid query type'));
        }

        /** @var Product|RepositoryError */
        $result = $this->productRepository->get($query->productId);

        if ($result instanceof RepositoryError) {
            return Result::failure($result);
        }

        return Result::success($result);
    }
}
