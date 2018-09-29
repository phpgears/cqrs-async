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

use Gears\CQRS\Async\Serializer\CommandSerializer;
use Gears\CQRS\Command;

abstract class AbstractCommandQueue implements CommandQueue
{
    /**
     * Command serializer.
     *
     * @var CommandSerializer
     */
    private $serializer;

    /**
     * AbstractCommandQueue constructor.
     *
     * @param CommandSerializer $serializer
     */
    public function __construct(CommandSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get serialized command.
     *
     * @param Command $command
     *
     * @return string
     */
    final protected function getSerializedCommand(Command $command): string
    {
        return $this->serializer->serialize($command);
    }
}
