<?php

namespace App\Application\Order\CreateOrder;

use App\Application\Repositories\User\UserRepository;
use App\Application\Repositories\Product\ProductRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Application\Repositories\Order\OrderRepository;
use App\Domain\Order\Order;
use App\Domain\OrderItem\OrderItem;
use DomainException;

final class CreateOrderHandler
{
    private UserRepository $userRepository;
    private ProductRepository $productRepository;
    private StockRepository $stockRepository;
    private OrderRepository $orderRepository;

    public function __construct(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        StockRepository $stockRepository,
        OrderRepository $orderRepository
    ) {
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->stockRepository = $stockRepository;
        $this->orderRepository = $orderRepository;
    }

    public function handle(CreateOrderCommand $command): Order
    {
        $user = $this->userRepository->findById($command->userId());

        if (!$user->isActive()) {
            throw new DomainException('Inactive user cannot create orders');
        }

        $order = new Order(
            id: uniqid(),
            userId: $user->id()
        );

        foreach ($command->items() as $itemData) {
            $product = $this->productRepository->findById($itemData['productId']);
            $stock = $this->stockRepository->findByProductId($product->id());

            $stock->reserve($itemData['quantity']);

            $orderItem = new OrderItem(
                id: uniqid(),
                productId: $product->id(),
                quantity: $itemData['quantity'],
                unitPrice: $product->price()
            );

            $order->addItem($orderItem);

            $this->stockRepository->save($stock);
        }

        $this->orderRepository->save($order);

        return $order;
    }
}
