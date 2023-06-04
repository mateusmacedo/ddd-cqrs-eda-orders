<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Commands\AddProductToOrder;
use App\Application\Handlers\{CalculateOrder, FetchOrder, FetchProduct, PlaceOrder, RegisterProduct, RemoveProductFromOrder};

enum Actions: string
{
    case ADD_PRODUCT_TO_ORDER = AddProductToOrder::class;
    case PLACE_ORDER = PlaceOrder::class;
    case REGISTER_PRODUCT = RegisterProduct::class;
    case REMOVE_PRODUCT_FROM_ORDER = RemoveProductFromOrder::class;
    case CALCULATE_ORDER = CalculateOrder::class;
    case FETCH_ORDER = FetchOrder::class;
    case FETCH_PRODUCT = FetchProduct::class;
}
