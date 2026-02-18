<?php

declare(strict_types=1);

namespace App\Application\Order\CancelOrder;

use App\Application\Common\TransactionManager;
use App\Application\Order\DTO\OrderDTO;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Order\Exceptions\UnauthorizedOrderException;
use App\Domain\Order\Order;

final class CancelOrderHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly StockRepository $stockRepository,
        private readonly TransactionManager $transactionManager,
    ) {}

    public function handle(CancelOrderCommand $command): OrderDTO
    {
        $order = $this->transactionManager->run(function () use ($command): Order {
            $order = $this->orderRepository->findByIdForUpdate($command->orderId);

            if (!$order->ownedBy($command->requesterId)) {
                throw UnauthorizedOrderException::notOwner($command->orderId);
            }

            foreach ($order->items() as $item) {
                $stock = $this->stockRepository->findByProductIdForUpdate($item->productId());
                $stock->release($item->quantity());
                $this->stockRepository->save($stock);
            }

            $order->markAsCancelled();
            $this->orderRepository->save($order);

            return $order;
        });

        // Dispatch domain events after transaction commits.
        // Guard prevents failures in unit tests that run without the full Laravel container.
        if (function_exists('app') && app()->bound('events')) {
            foreach ($order->pullDomainEvents() as $event) {
                event($event);
            }
        } else {
            $order->pullDomainEvents(); // drain the queue
        }

        return OrderDTO::fromDomain($order);
    }
}
