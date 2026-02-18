<?php

namespace App\Application\Order\CreateOrder;

use App\Common\IdGenerator;
use App\Application\Common\TransactionManager;
use App\Application\Repositories\User\UserRepository;
use App\Application\Repositories\Product\ProductRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Application\Repositories\Order\OrderRepository;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use DomainException;

final class CreateOrderHandler
{
    private IdGenerator $idGenerator;
    private UserRepository $userRepository;
    private ProductRepository $productRepository;
    private StockRepository $stockRepository;
    private OrderRepository $orderRepository;
    private TransactionManager $transactionManager;

    public function __construct(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        StockRepository $stockRepository,
        OrderRepository $orderRepository,
        IdGenerator $idGenerator,
        TransactionManager $transactionManager

    ) {
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->stockRepository = $stockRepository;
        $this->orderRepository = $orderRepository;
        $this->idGenerator = $idGenerator;
        $this->transactionManager = $transactionManager;
    }

    public function handle(CreateOrderCommand $command): Order
    {
        return $this->transactionManager->run(function () use ($command): Order {
            $user = $this->userRepository->findById($command->userId());

            if (!$user->isActive()) {
                throw new DomainException('Inactive user cannot create orders');
            }

            $order = new Order(
                id: $this->idGenerator->generate(),
                userId: $user->id()
            );

            foreach ($command->items() as $itemData) {
                $product = $this->productRepository->findById($itemData['productId']);
                $stock = $this->stockRepository->findByProductIdForUpdate($product->id());

                $stock->reserve($itemData['quantity']);

                $orderItem = new OrderItem(
                    id: $this->idGenerator->generate(),
                    productId: $product->id(),
                    quantity: $itemData['quantity'],
                    unitPrice: $product->price()
                );

                $order->addItem($orderItem);

                $this->stockRepository->save($stock);
            }

            $this->orderRepository->save($order);

            return $order;
        });
    }
}
