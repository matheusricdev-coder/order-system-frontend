<?php

namespace App\Application\Order\PayOrder;

use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Order\Order;
use DomainException;

final class PayOrderHandler
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

    public function handle(string $orderId): Order
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order->canBePaid()) {
            throw new DomainException('Order cannot be paid');
        }

        foreach ($order->items() as $item) {
            $stock = $this->stockRepository->findByProductId($item->productId());
            $stock->consume($item->quantity());
            $this->stockRepository->save($stock);
        }

        $order->markAsPaid();

        $this->orderRepository->save($order);

        return $order;
    }
}
