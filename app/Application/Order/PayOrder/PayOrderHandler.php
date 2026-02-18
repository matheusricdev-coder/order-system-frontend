<?php

declare(strict_types=1);

namespace App\Application\Order\PayOrder;

use App\Application\Common\DomainEventBus;
use App\Application\Common\TransactionManager;
use App\Application\Order\DTO\OrderDTO;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Order\Exceptions\UnauthorizedOrderException;
use App\Domain\Order\Order;

final class PayOrderHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly StockRepository $stockRepository,
        private readonly TransactionManager $transactionManager,
        private readonly DomainEventBus $domainEventBus,
    ) {}

    public function handle(PayOrderCommand $command): OrderDTO
    {
        $order = $this->transactionManager->run(function () use ($command): Order {
            $order = $this->orderRepository->findByIdForUpdate($command->orderId);

            if (!$order->ownedBy($command->requesterId)) {
                throw UnauthorizedOrderException::notOwner($command->orderId);
            }

            foreach ($order->items() as $item) {
                $stock = $this->stockRepository->findByProductIdForUpdate($item->productId());
                $stock->consume($item->quantity());
                $this->stockRepository->save($stock);
            }

            $order->markAsPaid();
            $this->orderRepository->save($order);

            return $order;
        });

        $this->domainEventBus->publish($order->pullDomainEvents());

        return OrderDTO::fromDomain($order);
    }
}
