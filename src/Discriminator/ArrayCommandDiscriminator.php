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
    private $commandTypes;

    /**
     * ArrayCommandDiscriminator constructor.
     *
     * @param string[] $commandTypes
     */
    public function __construct(array $commandTypes)
    {
        $this->commandTypes = \array_values($commandTypes);
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Command $command): bool
    {
        return \in_array($command->getCommandType(), $this->commandTypes, true);
    }
}
