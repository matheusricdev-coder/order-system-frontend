<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\GetOrder;

use App\Application\Order\GetOrder\GetOrderHandler;
use App\Application\Order\GetOrder\GetOrderQuery;
use App\Application\Order\GetOrder\GetOrderReadModelRepository;
use PHPUnit\Framework\TestCase;

final class GetOrderHandlerTest extends TestCase
{
    public function test_it_delegates_to_read_model_repository(): void
    {
        $repository = new FakeGetOrderReadModelRepository([
            'id' => 'o-1',
            'status' => 'pending',
        ]);

        $handler = new GetOrderHandler($repository);

        $result = $handler->handle(new GetOrderQuery(orderId: 'o-1', requesterId: 'u-1'));

        self::assertSame(['o-1', 'u-1'], $repository->lastCall);
        self::assertSame('o-1', $result['id']);
    }
}

final class FakeGetOrderReadModelRepository implements GetOrderReadModelRepository
{
    /** @var string[]|null */
    public ?array $lastCall = null;

    /** @param array<string,mixed> $result */
    public function __construct(private array $result)
    {
    }

    public function getByIdForRequester(string $orderId, string $requesterId): array
    {
        $this->lastCall = [$orderId, $requesterId];

        return $this->result;
    }
}
