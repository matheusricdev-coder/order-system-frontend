<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\PayOrder;

use App\Application\Common\TransactionManager;
use App\Application\Order\PayOrder\PayOrderCommand;
use App\Application\Order\PayOrder\PayOrderHandler;
use App\Application\Repositories\Order\OrderRepository;
use App\Application\Repositories\Stock\StockRepository;
use App\Domain\Common\Money;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderStatus;
use App\Domain\Stock\Stock;
use DomainException;
use PHPUnit\Framework\TestCase;

final class PayOrderHandlerTest extends TestCase
{
    public function test_it_pays_order_inside_transaction_and_locks_rows(): void
    {
        $order = new Order('o-1', 'u-1');
        $order->addItem(new OrderItem('i-1', 'p-1', 2, new Money(1000, 'BRL')));

        $stock = new Stock('s-1', 'p-1', 10);
        $stock->reserve(2);

        $transactionManager = new TransactionManagerSpy();
        $orderRepository    = new InMemoryOrderRepository($order);
        $stockRepository    = new InMemoryStockRepository($stock);

        $handler = new PayOrderHandler($orderRepository, $stockRepository, $transactionManager);

        $dto = $handler->handle(new PayOrderCommand(orderId: 'o-1', requesterId: 'u-1'));

        self::assertSame(1, $transactionManager->runCalls);
        self::assertSame(['o-1'], $orderRepository->forUpdateLookups);
        self::assertSame(['p-1'], $stockRepository->forUpdateLookups);
        self::assertSame('paid', $dto->status);
    }

    public function test_it_rejects_order_that_cannot_be_paid(): void
    {
        $order = Order::reconstitute('o-1', 'u-1', OrderStatus::CANCELLED, []);

        $handler = new PayOrderHandler(
            new InMemoryOrderRepository($order),
            new InMemoryStockRepository(new Stock('s-1', 'p-1', 10)),
            new TransactionManagerSpy()
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order cannot be paid');

        $handler->handle(new PayOrderCommand(orderId: 'o-1', requesterId: 'u-1'));
    }

    public function test_it_rejects_when_requester_is_not_owner(): void
    {
        $order = new Order('o-1', 'u-1');
        $order->addItem(new OrderItem('i-1', 'p-1', 1, new Money(1000, 'BRL')));

        $stock = new Stock('s-1', 'p-1', 10);
        $stock->reserve(1);

        $handler = new PayOrderHandler(
            new InMemoryOrderRepository($order),
            new InMemoryStockRepository($stock),
            new TransactionManagerSpy()
        );

        $this->expectException(DomainException::class);

        $handler->handle(new PayOrderCommand(orderId: 'o-1', requesterId: 'other-user'));
    }

    public function test_paid_order_emits_domain_event(): void
    {
        $order = new Order('o-1', 'u-1');
        $order->addItem(new OrderItem('i-1', 'p-1', 1, new Money(1000, 'BRL')));

        $stock = new Stock('s-1', 'p-1', 10);
        $stock->reserve(1);

        $handler = new PayOrderHandler(
            new InMemoryOrderRepository($order),
            new InMemoryStockRepository($stock),
            new TransactionManagerSpy()
        );

        $handler->handle(new PayOrderCommand(orderId: 'o-1', requesterId: 'u-1'));

        // After pullDomainEvents() the aggregate should have no pending events
        self::assertEmpty($order->pullDomainEvents());
    }
}

final class TransactionManagerSpy implements TransactionManager
{
    public int $runCalls = 0;

    public function run(callable $fn): mixed
    {
        $this->runCalls++;
        return $fn();
    }
}

final class InMemoryOrderRepository implements OrderRepository
{
    /** @var string[] */
    public array $forUpdateLookups = [];

    public function __construct(private Order $order) {}

    public function save(Order $order): void
    {
        $this->order = $order;
    }

    public function findById(string $id): Order
    {
        throw new DomainException('findById should not be used in write use case');
    }

    public function findByIdForUpdate(string $id): Order
    {
        $this->forUpdateLookups[] = $id;
        return $this->order;
    }
}

final class InMemoryStockRepository implements StockRepository
{
    /** @var string[] */
    public array $forUpdateLookups = [];

    public function __construct(private Stock $stock) {}

    public function findByProductId(string $productId): Stock
    {
        throw new DomainException('findByProductId should not be used in write use case');
    }

    public function findByProductIdForUpdate(string $productId): Stock
    {
        $this->forUpdateLookups[] = $productId;
        return $this->stock;
    }

    public function save(Stock $stock): void
    {
        $this->stock = $stock;
    }
}
