<?php

declare(strict_types=1);

namespace App\Application\Order\FailPayment;

use App\Application\Common\DomainEventBus;
use App\Application\Common\TransactionManager;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;

/**
 * Handles Stripe payment_intent.payment_failed webhooks.
 *
 * This handler:
 *   1. Finds the order by PaymentIntent ID.
 *   2. Releases the reserved stock back to available.
 *   3. Marks the order as CANCELLED and fires the OrderCancelled domain event.
 */
final class FailPaymentHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly StockRepository $stockRepository,
        private readonly TransactionManager $transactionManager,
        private readonly DomainEventBus $domainEventBus,
    ) {}

    public function handle(FailPaymentCommand $command): void
    {
        $order = $this->transactionManager->run(function () use ($command): Order {
            $order = $this->orderRepository->findByPaymentIntentId($command->paymentIntentId);

            // Idempotency guard — webhook may be delivered multiple times
            if ($order->status() === OrderStatus::CANCELLED) {
                return $order;
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

        $this->domainEventBus->publish($order->pullDomainEvents());
    }
}
