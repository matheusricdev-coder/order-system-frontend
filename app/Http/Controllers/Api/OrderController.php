<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Order\CancelOrder\CancelOrderCommand;
use App\Application\Order\CancelOrder\CancelOrderHandler;
use App\Application\Order\CreateOrder\CreateOrderCommand;
use App\Application\Order\CreateOrder\CreateOrderHandler;
use App\Application\Order\GetOrder\GetOrderHandler;
use App\Application\Order\GetOrder\GetOrderQuery;
use App\Application\Order\ListOrders\ListOrdersHandler;
use App\Application\Order\ListOrders\ListOrdersQuery;
use App\Application\Order\PayOrder\PayOrderCommand;
use App\Application\Order\PayOrder\PayOrderHandler;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Requests\CreateOrderRequest;
use App\Models\UserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OrderController extends Controller
{
    public function create(
        CreateOrderRequest $request,
        CreateOrderHandler $handler,
    ): JsonResponse {
        $authUser = $this->authUser($request);
        $payload  = $request->validated();

        $dto = $handler->handle(new CreateOrderCommand(
            userId: $authUser->id,
            items: $payload['items'],
        ));

        return response()->json(
            $dto->toArray() + ['correlationId' => $this->correlationId($request)],
            201,
        );
    }

    public function pay(
        string $id,
        Request $request,
        PayOrderHandler $handler,
    ): JsonResponse {
        $authUser = $this->authUser($request);

        $dto = $handler->handle(new PayOrderCommand(
            orderId: $id,
            requesterId: $authUser->id,
        ));

        return response()->json(
            $dto->toArray() + ['correlationId' => $this->correlationId($request)]
        );
    }

    public function cancel(
        string $id,
        Request $request,
        CancelOrderHandler $handler,
    ): JsonResponse {
        $authUser = $this->authUser($request);

        $dto = $handler->handle(new CancelOrderCommand(
            orderId: $id,
            requesterId: $authUser->id,
        ));

        return response()->json([
            'orderId'       => $dto->id,
            'status'        => $dto->status,
            'correlationId' => $this->correlationId($request),
        ]);
    }

    public function show(
        string $id,
        Request $request,
        GetOrderHandler $handler,
    ): JsonResponse {
        $authUser = $this->authUser($request);

        return response()->json(
            $handler->handle(new GetOrderQuery(
                orderId: $id,
                requesterId: $authUser->id,
            ))
        );
    }

    public function index(
        Request $request,
        ListOrdersHandler $handler,
    ): JsonResponse {
        $authUser = $this->authUser($request);

        return response()->json(
            $handler->handle(new ListOrdersQuery(
                requesterId: $authUser->id,
                status: $request->query('status'),
                perPage: (int) $request->query('perPage', 15),
                page: (int) $request->query('page', 1),
            ))
        );
    }

    private function authUser(Request $request): UserModel
    {
        /** @var UserModel $user */
        $user = $request->user();

        return $user;
    }

    private function correlationId(Request $request): ?string
    {
        return $request->attributes->get(CorrelationIdMiddleware::ATTRIBUTE);
    }
}
