<?php

namespace App\Application\Order\CancelOrder;

use App\Application\Common\TransactionManager;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use DomainException;

final class CancelOrderHandler
{
    private OrderRepository $orderRepository;
    private StockRepository $stockRepository;
    private TransactionManager $transactionManager;

    public function __construct(
        OrderRepository $orderRepository,
        StockRepository $stockRepository,
        TransactionManager $transactionManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->stockRepository = $stockRepository;
        $this->transactionManager = $transactionManager;
    }

    public function handle(string $orderId): void
    {
        $this->transactionManager->run(function () use ($orderId): void {
            $order = $this->orderRepository->findByIdForUpdate($orderId);

            if (!$order->canBeCancelled()) {
                throw new DomainException('Order cannot be cancelled');
            }

            foreach ($order->items() as $item) {
                $stock = $this->stockRepository->findByProductIdForUpdate($item->productId());
                $stock->release($item->quantity());
                $this->stockRepository->save($stock);
            }

            $order->markAsCancelled();

            $this->orderRepository->save($order);
        });
    }
}
