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

class ClassCommandDiscriminator implements CommandDiscriminator
{
    /**
     * @var string
     */
    private $className;

    /**
     * AsyncCommandClassDiscriminator constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldEnqueue(Command $command): bool
    {
        return \is_a($command, $this->className);
    }
}
