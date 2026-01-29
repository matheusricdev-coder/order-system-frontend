<?php

namespace App\Application\Order\CancelOrder;

use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use DomainException;

final class CancelOrderHandler
{
    private OrderRepository $orderRepository;
    private StockRepository $stockRepository;

    public function __construct(
        OrderRepository $orderRepository,
        StockRepository $stockRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->stockRepository = $stockRepository;
    }

    public function handle(string $orderId): void
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order->canBeCancelled()) {
            throw new DomainException('Order cannot be cancelled');
        }

        foreach ($order->items() as $item) {
            $stock = $this->stockRepository->findByProductId($item->productId());
            $stock->release($item->quantity());
            $this->stockRepository->save($stock);
        }

        $order->markAsCancelled();

        $this->orderRepository->save($order);
    }
}
