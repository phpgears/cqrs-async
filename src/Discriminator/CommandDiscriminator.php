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

namespace Gears\CQRS\Async\Discriminator;

use Gears\CQRS\Command;

interface CommandDiscriminator
{
    /**
     * Should command be enqueued.
     *
     * @param Command $command
     *
     * @return bool
     */
    public function shouldEnqueue(Command $command): bool;
}
