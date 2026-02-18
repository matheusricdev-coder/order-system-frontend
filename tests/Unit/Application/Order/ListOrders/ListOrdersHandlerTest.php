<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Order\ListOrders;

use App\Application\Order\ListOrders\ListOrdersHandler;
use App\Application\Order\ListOrders\ListOrdersQuery;
use App\Application\Order\ListOrders\ListOrdersReadModelRepository;
use PHPUnit\Framework\TestCase;

final class ListOrdersHandlerTest extends TestCase
{
    public function test_it_delegates_to_read_model_repository(): void
    {
        $repository = new FakeListOrdersReadModelRepository([
            'data' => [['id' => 'o-1']],
            'total' => 1,
        ]);

        $handler = new ListOrdersHandler($repository);

        $result = $handler->handle(new ListOrdersQuery(
            requesterId: 'u-1',
            status: 'pending',
            perPage: 10,
            page: 2,
        ));

        self::assertSame(['u-1', 'pending', 10, 2], $repository->lastCall);
        self::assertSame(1, $result['total']);
    }
}

final class FakeListOrdersReadModelRepository implements ListOrdersReadModelRepository
{
    /** @var array<int,mixed>|null */
    public ?array $lastCall = null;

    /** @param array<string,mixed> $result */
    public function __construct(private array $result)
    {
    }

    public function listForRequester(string $requesterId, ?string $status, int $perPage, int $page): array
    {
        $this->lastCall = [$requesterId, $status, $perPage, $page];

        return $this->result;
    }
}
