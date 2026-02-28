<?php

declare(strict_types=1);

namespace App\Application\Order\FinalizePayment;

use App\Application\Common\DomainEventBus;
use App\Application\Common\TransactionManager;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Order\Order;

/**
 * Finalizes an order after Stripe confirms payment_intent.succeeded.
 *
 * This handler:
 *   1. Finds the order by PaymentIntent ID (with DB lock for safety).
 *   2. Consumes the reserved stock for each item.
 *   3. Marks the order as PAID and fires the OrderPaid domain event.
 *
 * This is the authoritative place where stock is consumed — never earlier.
 * This ensures stock is only decremented when money has actually moved.
 */
final class FinalizePaymentHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly StockRepository $stockRepository,
        private readonly TransactionManager $transactionManager,
        private readonly DomainEventBus $domainEventBus,
    ) {}

    public function handle(FinalizePaymentCommand $command): void
    {
        $order = $this->transactionManager->run(function () use ($command): Order {
            $order = $this->orderRepository->findByPaymentIntentId($command->paymentIntentId);

            // Idempotency guard — webhook may be delivered multiple times
            if ($order->status() === \App\Domain\Order\OrderStatus::PAID) {
                return $order;
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
    }
}
