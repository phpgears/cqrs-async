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

use Gears\CQRS\Command;

interface CommandSerializer
{
    /**
     * Get serialized from command.
     *
     * @param Command $command
     *
     * @return string
     */
    public function serialize(Command $command): string;

    /**
     * Get command from serialized.
     *
     * @param string $serialized
     *
     * @throws \Gears\CQRS\Async\Serializer\Exception\CommandSerializationException
     *
     * @return Command
     */
    public function fromSerialized(string $serialized): Command;
}
