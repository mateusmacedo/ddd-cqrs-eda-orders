<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Commands\{AddProductToOrder, CreateOrder, PlaceOrder, RegisterProduct, RemoveProductFromOrder};
use App\Application\Queries\{FetchOrder, FetchProduct, CalculateOrder};

enum Actions: string
{
    case ADD_PRODUCT_TO_ORDER = AddProductToOrder::class;
    case PLACE_ORDER = PlaceOrder::class;
    case REGISTER_PRODUCT = RegisterProduct::class;
    case REMOVE_PRODUCT_FROM_ORDER = RemoveProductFromOrder::class;
    case CALCULATE_ORDER = CalculateOrder::class;
    case FETCH_ORDER = FetchOrder::class;
    case FETCH_PRODUCT = FetchProduct::class;
    case CREATE_ORDER = CreateOrder::class;
}
