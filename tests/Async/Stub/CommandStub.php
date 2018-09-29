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

use Gears\CQRS\AbstractCommand;

/**
 * Command stub class.
 */
class CommandStub extends AbstractCommand
{
    /**
     * Instantiate command.
     *
     * @param mixed[] $parameters
     *
     * @return self
     */
    public static function instance(array $parameters = []): self
    {
        return new self($parameters);
    }
}
