<?php

declare(strict_types=1);

namespace App\Application\Common;

interface DomainEventBus
{
    /**
     * @param object[] $events
     */
    public function publish(array $events): void;
}
