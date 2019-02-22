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

final class ArrayCommandDiscriminator implements CommandDiscriminator
{
    /**
     * @var string[]
     */
    private $commands;

    /**
     * ArrayCommandDiscriminator constructor.
     *
     * @param string[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = \array_values($commands);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Command $command): bool
    {
        return \in_array(\get_class($command), $this->commands, true);
    }
}
