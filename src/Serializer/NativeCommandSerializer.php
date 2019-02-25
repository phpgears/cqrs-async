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

namespace Gears\CQRS\Async\Serializer;

use Gears\CQRS\Async\Serializer\Exception\CommandSerializationException;
use Gears\CQRS\Command;

final class NativeCommandSerializer implements CommandSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Command $command): string
    {
        return \serialize($command);
    }

    /**
     * {@inheritdoc}
     */
    public function fromSerialized(string $serialized): Command
    {
        $command = \unserialize($serialized);

        if (!$command instanceof Command) {
            throw new CommandSerializationException('Invalid unserialized command');
        }

        return $command;
    }
}
