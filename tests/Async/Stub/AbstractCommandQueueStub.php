<?php

/*
 * cqrs-async (https://github.com/phpgears/cqrs-async).
 * Async decorator for CQRS command bus.
 *
 * @license MIT
 * @link https://github.com/phpgears/cqrs-async
 * @author Julián Gutiérrez <juliangut@gmail.com>
 */

declare(strict_types=1);

namespace Gears\CQRS\Async\Tests\Stub;

use Gears\CQRS\Async\AbstractCommandQueue;
use Gears\CQRS\Command;

/**
 * AbstractCommandQueueStub stub class.
 */
class AbstractCommandQueueStub extends AbstractCommandQueue
{
    /**
     * {@inheritdoc}
     */
    public function send(Command $command): void
    {
        $this->getSerializedCommand($command);

        // noop, should enqueue command
    }
}
