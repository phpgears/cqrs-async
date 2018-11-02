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

use Gears\CQRS\Async\Discriminator\CommandDiscriminator;
use Gears\CQRS\Command;
use Gears\CQRS\CommandBus;

class AsyncCommandBus implements CommandBus
{
    /**
     * Wrapped command bus.
     *
     * @var CommandBus
     */
    private $wrappedCommandBus;

    /**
     * Command queue.
     *
     * @var CommandQueue
     */
    private $queue;

    /**
     * Command discriminator.
     *
     * @var CommandDiscriminator
     */
    private $discriminator;

    /**
     * AsyncCommandBus constructor.
     *
     * @param CommandBus           $wrappedCommandBus
     * @param CommandQueue         $queue
     * @param CommandDiscriminator $discriminator
     */
    public function __construct(
        CommandBus $wrappedCommandBus,
        CommandQueue $queue,
        CommandDiscriminator $discriminator
    ) {
        $this->wrappedCommandBus = $wrappedCommandBus;
        $this->discriminator = $discriminator;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Gears\CQRS\Async\Exception\CommandQueueException
     */
    final public function handle(Command $command): void
    {
        if (!$command instanceof ReceivedCommand && $this->discriminator->shouldEnqueue($command)) {
            $this->queue->send($command);

            return;
        }

        if ($command instanceof ReceivedCommand) {
            $command = $command->getOriginalCommand();
        }

        $this->wrappedCommandBus->handle($command);
    }
}
