<?php

namespace App\Application\Order\PayOrder;

use App\Application\Common\TransactionManager;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Order\Order;
use DomainException;

final class PayOrderHandler
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

    public function handle(string $orderId): Order
    {
        return $this->transactionManager->run(function () use ($orderId): Order {
            $order = $this->orderRepository->findByIdForUpdate($orderId);

            if (!$order->canBePaid()) {
                throw new DomainException('Order cannot be paid');
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
    }
}
