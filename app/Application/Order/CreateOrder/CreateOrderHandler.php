<?php

declare(strict_types=1);

namespace App\Application\Order\CreateOrder;

use App\Application\Common\TransactionManager;
use App\Application\Order\DTO\OrderDTO;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Product\ProductRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Application\Repositories\User\UserRepository;
use App\Common\IdGenerator;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\User\Exceptions\InactiveUserException;

final class CreateOrderHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
        private readonly StockRepository $stockRepository,
        private readonly OrderRepository $orderRepository,
        private readonly IdGenerator $idGenerator,
        private readonly TransactionManager $transactionManager,
    ) {}

    public function handle(CreateOrderCommand $command): OrderDTO
    {
        $order = $this->transactionManager->run(function () use ($command): Order {
            $user = $this->userRepository->findById($command->userId());

            if (!$user->isActive()) {
                throw InactiveUserException::forUser($user->id());
            }

            $order = new Order(
                id: $this->idGenerator->generate(),
                userId: $user->id(),
            );

            foreach ($command->items() as $itemData) {
                $product = $this->productRepository->findById($itemData['productId']);
                $stock   = $this->stockRepository->findByProductIdForUpdate($product->id());

                $stock->reserve($itemData['quantity']);

                $order->addItem(new OrderItem(
                    id: $this->idGenerator->generate(),
                    productId: $product->id(),
                    quantity: $itemData['quantity'],
                    unitPrice: $product->price(),
                ));

                $this->stockRepository->save($stock);
            }

            $order->recordCreated();
            $this->orderRepository->save($order);

            return $order;
        });

        // Dispatch domain events after transaction commits (outside DB transaction boundary).
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

