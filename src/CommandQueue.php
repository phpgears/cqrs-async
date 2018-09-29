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

namespace Gears\CQRS\Async;

use Gears\CQRS\Command;

interface CommandQueue
{
    /**
     * Send command to queue.
     *
     * @param Command $command
     *
     * @throws \Gears\CQRS\Async\Exception\CommandQueueException
     */
    public function send(Command $command): void;
}
