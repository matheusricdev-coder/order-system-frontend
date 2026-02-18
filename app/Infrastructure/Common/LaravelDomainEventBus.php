<?php

declare(strict_types=1);

namespace App\Infrastructure\Common;

use App\Application\Common\DomainEventBus;

final class LaravelDomainEventBus implements DomainEventBus
{
    public function publish(array $events): void
    {
        foreach ($events as $event) {
            event($event);
        }
    }
}
